import React, { Component } from 'react';
import ReactDOM from 'react-dom';
//
export default class PaymentRechargeHistory extends Component {
	
	constructor(props) {
		super(props);
		this.state = {
			error: null,
			mods: null,
			totalPage: 0,
			currentPage: 0,
		}
	}
	
	componentDidMount() {
		axios.get('/api/topup/historyR?access_token=' + API_TOKEN)
		.then((resp) => {
			this.setState({mods: resp.data.mods, totalPage: resp.data.total_page, currentPage: 0, error: null});
		})
		.catch((ex) => {
			this.setState({error: ex});
		});
	}
	
	fetchPage(e)
	{
		axios.get('/api/topup/historyR?access_token=' + API_TOKEN + '&page=' + e.currentTarget.dataset.id ) 
		.then((resp) => {
			this.setState({mods: resp.data.mods, totalPage: resp.data.total_page, currentPage: resp.data.current_page, error: null});
		})
		.catch((ex) => {
			this.setState({error: ex});
		});
	}
	
    render() {
    	const mods = this.state.mods;
    	const totalPage = this.state.totalPage;
    	let modRows = [];
    	if (mods != null)
	    	mods.map((mod, index) => {
	    		modRows.push((
	    				<tr key={index}>
	    					<td>{mod.acc_type == 0 ? "Tk Phụ" : "Tk Chính"}</td>
	    					<td>{mod.balance}</td>
	    					<td>{mod.reason}</td>
	    					<td>{new Date(mod.time).toLocaleDateString("vi-VN")}-{new Date(mod.time).toLocaleTimeString("vi-VN")}</td>
	    				</tr>
	    				));
	    	});
    	let pages = [];
    	if (totalPage > 1) {
    		for (let i=1; i<=totalPage; ++i) {
    			pages.push((<li key={i} id={i} onClick={this.fetchPage.bind(this)} data-id={i}>{i}</li>));
    		}
    	}
        return (
            <div id="my-payment-history">
            	<div id="mod-history">
	            	<table>
		        		<tr>
		        			<th>Type</th>
		        			<th>Balance</th>
		        			<th>Reason</th>
		        			<th>Time</th>
		        		</tr>
		        		{modRows}
		        	</table>
		        </div>
		        <div id="mod-history-pages">
		        	<ul>
		        		{pages}
		        	</ul>
		        </div>
            </div>
        );
    }
}

if (document.getElementById('my-payment-recharge-history')) {
    ReactDOM.render(<PaymentRechargeHistory />, document.getElementById('my-payment-recharge-history'));
}
