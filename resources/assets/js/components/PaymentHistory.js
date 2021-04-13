import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import DelayCard from './DelayCard';
//
export default class PaymentHistory extends Component {
	
	constructor(props) {
		super(props);
		this.state = {
				error: null,
				submits: null,
				mods: null,
		}
	}
	
	componentDidMount() {
		axios.get('/api/topup/history?access_token=' + API_TOKEN)
		.then((resp) => {
			this.setState({submits: resp.data.submits, mods: resp.data.mods});
		})
		.catch((ex) => {
			this.setState({error: ex});
		});
	}
	
    render() {
    	const submits = this.state.submits;
    	const mods = this.state.mods;
    	// build submits tables
    	var submitRows = [];
    	if (submits != null)
	    	submits.map((sub) => {
	    		if (sub.delay && sub.value == 0) {
	    			submitRows.push((
							<tr>
								<td>{trans('status.' + sub.status)}</td>
		    					<td>{sub.serial}</td>
		    					<td>{sub.password}</td>
		    					<td><DelayCard mapping={sub.mapping}/></td>
		    					<td>0</td>
		    					<td>0</td>
		    					<td>{sub.time}</td>
		    				</tr>));
	    		} else {
		    		submitRows.push((
		    				<tr>
		    					<td>{trans('status.' + sub.status)}</td>
		    					<td>{sub.serial}</td>
		    					<td>{sub.password}</td>
		    					<td>{sub.value}</td>
		    					<td>{sub.penalty}</td>
		    					<td>{sub.final_value}</td>
		    					<td>{sub.time}</td>
		    				</tr>
		    				));
	    		}
	    	});
    	var modRows = [];
    	if (mods != null)
	    	mods.map((mod) => {
	    		modRows.push((
	    				<tr>
	    					<td>{mod.acc_type}</td>
	    					<td>{mod.balance}</td>
	    					<td>{mod.reason}</td>
	    					<td>{mod.time}</td>
	    				</tr>
	    				));
	    	});
    	
        return (
            <div id="my-payment-history">
            	<div id="sub-history">
	            	<table>
	            		<tr>
	            			<th>Status</th>
	            			<th>Card serial</th>
	            			<th>Card password</th>
	            			<th>Card value</th>
	            			<th>Penalty</th>
	            			<th>Income</th>
	            			<th>Card time</th>
	            		</tr>
	            		{submitRows}
	            	</table>
            	</div>
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
            </div>
        );
    }
}

if (document.getElementById('my-payment-history')) {
    ReactDOM.render(<PaymentHistory />, document.getElementById('my-payment-history'));
}
