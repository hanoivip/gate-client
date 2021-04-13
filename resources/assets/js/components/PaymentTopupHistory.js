import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import DelayCard from './DelayCard';
import { trans } from './Helpers';

export default class PaymentTopupHistory extends Component {
	
	constructor(props) {
		super(props);
		this.state = {
			error: null,
			submits: null,
			totalPage: 0,
			currentPage: 0,
		}
	}
	
	componentDidMount() {
		axios.get('/api/topup/historyP?access_token=' + API_TOKEN)
		.then((resp) => {
			this.setState({submits: resp.data.submits, totalPage: resp.data.total_page, currentPage: resp.data.current_page, error: null});
		})
		.catch((ex) => {
			this.setState({error: ex});
		});
	}
	
	fetchPage(e)
	{
		console.log('open history page..' + e.currentTarget.dataset.id );
		axios.get('/api/topup/historyP?access_token=' + API_TOKEN + '&page=' + e.currentTarget.dataset.id ) 
		.then((resp) => {
			this.setState({submits: resp.data.submits, totalPage: resp.data.total_page, currentPage: resp.data.current_page, error: null});
		})
		.catch((ex) => {
			this.setState({error: ex});
		});
	}
	
    render() {
    	const submits = this.state.submits;
    	const totalPage = this.state.totalPage;
    	var submitRows = [];
    	if (submits != null)
	    	submits.map((sub, index) => {
	    		if (sub.delay && sub.value == 0) {
	    			submitRows.push((
							<tr key={index}>
								<td className={'status' + sub.status}>{trans('status.' + sub.status)}</td>
								<td>{sub.password}</td>
								<td>{sub.dvalue}</td>
		    					<td><DelayCard mapping={sub.mapping}/></td>
		    					<td>0%</td>
		    					<td>0</td>
		    					<td>{new Date(sub.time).toLocaleDateString("vi-VN")}-{new Date(sub.time).toLocaleTimeString("vi-VN")}</td>
		    				</tr>));
	    		} else {
		    		submitRows.push((
		    				<tr key={index}>
		    					<td className={'status' + sub.status}>{trans('status.' + sub.status)}</td>
		    					<td>{sub.password}</td>
		    					<td>{sub.dvalue}</td>
		    					<td>{sub.value}</td>
		    					<td>{sub.penalty}%</td>
		    					<td>{Math.min(sub.dvalue, sub.value) * (100-sub.penalty) / 100}</td>
		    					<td>{new Date(sub.time).toLocaleDateString("vi-VN")}-{new Date(sub.time).toLocaleTimeString("vi-VN")}</td>
		    				</tr>));
	    		}
	    	});
    	let pages = [];
    	if (totalPage > 1) {
    		for (let i=1; i<=totalPage; ++i) {
    			pages.push((<li key={i} id={i} onClick={this.fetchPage.bind(this)} data-id={i}>{i}</li>));
    		}
    	}
    	
        return (
            <div id="my-payment-history">
            	<div id="sub-history">
	            	<table>
	            		<tr>
	            			<th>Status</th>
	            			<th>Card password</th>
	            			<th>Choosen value</th>
	            			<th>Card value</th>
	            			<th>Penalty</th>
	            			<th>Income</th>
	            			<th>Card time</th>
	            		</tr>
	            		{submitRows}
	            	</table>
            	</div>
            	<div id="sub-history-pages">
            		<ul>
            			{pages}
            		</ul>
            	</div>
            </div>
        );
    }
}

if (document.getElementById('my-payment-topup-history')) {
    ReactDOM.render(<PaymentTopupHistory />, document.getElementById('my-payment-topup-history'));
}
