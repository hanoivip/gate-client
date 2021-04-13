import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import PaymentHistory from './PaymentHistory';
// Querying delay submission
export default class DelayCard extends Component {
	constructor(props) {
		super(props);
		this.state = {
				retry: 1,
				trans: null,
		};
		this.timer = null;
	}
	
	componentWillUnmount () {
	    clearInterval(this.timer);
	}
	
	tick() {
		const retry = this.state.retry;
		if (retry >= 4) {
			console.log("This delay card might be too sloooow. Stop query..");
			clearInterval(this.timer);
		}
		else {
			const mapping = this.props.mapping;
			axios.get("/api/topup/query?mapping=" + mapping)
			.then((resp) => {
				this.setState({retry: retry+1, trans: resp.data.result});
			})
			.catch((err) => {
				console.log("Query transaction status error" + err);
				this.setState({retry: retry+1, trans: this.state.trans});
			});
		}
	}
	
	//https://codepen.io/jurekbarth/pen/pgYGBm
	componentDidMount() {
		this.timer = setInterval(this.tick.bind(this), 5000);
	}
	
	renderLoading() {
		console.log("retry " + this.state.retry);
		return (
				<div>
					<img src="img/loading.gif"/>
					<p>Querying at {this.state.retry}-th</p>
				</div>
				);
	}
	
	render() {
		const state = this.state;
		if (state.trans == null) {
			if (state.retry < 4)
				return this.renderLoading();
			else
				return <p>This card processing seems so long, please check it at the Topup History</p>;
		}
		if (state.trans.value > 0) {
			return <p>Card processed, your card value is: {state.trans.value}</p>
		}
		else {
			if (state.trans.success == false) {
				return <p>Card processed, but your card is wrong, you have 0 point</p>
			}
			else {
				if (state.retry < 4)
					return this.renderLoading();
				else
					return <p>This card processing seems so long, please check it at the Topup History</p>;
			}
		}
	}
}