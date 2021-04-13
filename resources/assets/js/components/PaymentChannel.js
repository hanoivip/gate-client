import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import DelayCard from './DelayCard';
import PaymentRule from './PaymentRule';
import { trans } from './Helpers';
// Payment channel input & interactions
// Click into links
// Or input info...
export default class PaymentChannel extends Component {
	constructor(props) {
		super(props);
		this.state = {
				error: null,
				params: null,
				input: {
					serial: null,
					password: null,
				},
				submitted: false,
				result_error: null,
				result: null,
		};
	}
	
	componentWillUnmount () {
		this.setState({
				error: null,
				params: null,
				input: {
					serial: null,
					password: null,
				},
				submitted: false,
				result_error: null,
				result: null,
		});
	}
	
  componentDidMount() {
	  	const type = this.props.type;
	  	const dvalue = this.props.dvalue;
	  	var uri = "/api/topup/select?access_token=" + API_TOKEN + "&type=" + type + "&dvalue=" + dvalue;
	    fetch(uri, {
	    	method: 'GET',
	    	headers: {
	    		'X-Requested-With': 'XMLHttpRequest',
	    	}
	    })
      .then(res => res.json())
      .then(
        (result) => {
          this.setState({params: result.result});
        },
        (error) => {
          this.setState({error: error});
        }
      );
  }
  
  onSerialChanged(e) {
	  this.state.input.serial = e.target.value;
  }
  
  onPasswordChanged(e) {
	  this.state.input.password = e.target.value;
  }
  
  handlePrepaid(e) {
	  console.log("Handle prepaid");
	  e.preventDefault();
	  if (this.state.input.serial == null || this.state.input.password == null) {
		  alert('You have to input serial/password first!');
		  return;
	  }
	  const data=new FormData(e.target);
	  const uri="/api/topup/do?access_token=" + API_TOKEN;
	  axios.post(uri, data)
      .then(
        (result) => {
        	//console.log(result);
        	//console.log(result.data);
        	//console.log(result.data.result);
          this.setState({
            params: this.state.params,
            submitted: true,
            result: result.data.result,
          });
        })
        .catch((error) => {
        	this.setState({
                params: this.state.params,
                submitted: true,
                result_error: error,
              });
        });
	  this.setState({params: this.state.params, error: null, result_error: null, submitted: true});
  }  
  
  handlePostpaid(e) {
	  console.log("Handle postpaid");
  }
  
  render() {
		if (this.state.error != null)
			return this.renderMalfunction();
	    if (this.state.params == null)
	    	return this.renderLoading();
	    else 
	    	return this.renderPrepaid();
	}
  
  render1() {
		if (this.state.error != null)
			return this.renderMalfunction();
	    if (this.state.params == null)
	    	return this.renderLoading();
	    else if (this.state.submitted == false) {
	    	if (typeof this.state.result == "string")
	    		return this.renderError();
	    	else 
	    		return this.renderPrepaid();
	    } else {
	    	return this.renderResult();
	    }
	}
  
  	renderResult() {
  		if (this.state.result_error != null)
  			return (
  					<div>
  						<p>Card topup exception</p>
  						<button>Nạp lại</button>
  					</div>
  					);
		else
		{
			const result = this.state.result;
			if (result == null)
				return this.renderLoading();
			var refresher = null;
			if (result.hasOwnProperty('delay') && result.delay)
				refresher = <DelayCard mapping={result.mapping}/>
			if (result.hasOwnProperty('message'))
				return (
					<div>
						<p>{result.message}</p>
						{refresher}
					</div>
					);
			else if (result.hasOwnProperty('error_message')) 
				return <p>{result.error_message}</p>
		}
  	}
	
	renderLoading() {
		return <div id="my-payment-channel"><img src="img/loading.gif"/></div>;
	}
	
	renderMalfunction() {
		return <p>Please try again later or contact support!</p>;
	}
	
	renderError() {
		const message = this.state.params;
		return <p>{message}</p>;
	}
	
	renderPrepaid() {
		var hidens = [];
		const params = this.state.params;
		Object.keys(params).forEach((p) => {
			hidens.push(<input type="hidden" key={p} id={p} name={p} value={params[p]}/>);
		});
		var note = "";
		if (this.props.need_dvalue) {
			note = (
					<div id="note">
						<p>You chosen the value of {this.props.dvalue}. Please read our rule carefully:</p>
						<PaymentRule />
					</div>	
					);
		}
		var button = null;
		if (!this.state.submitted) {
			button = <button>{trans('ui.btn_topup')}</button>;
		} else {
			if (this.state.result_error != null) {
				button = (
						<div>
							<button>{trans('ui.btn_retopup')}</button>
							<div id="error">
								<p>System error, please retry!</p>
							</div>
						</div>
						);
			} else {
				const result = this.state.result;
				if (result == null) {
					button = this.renderLoading();
				} else if (typeof result == "string") {
					button = (
							<div id="error">
									<p>{result}</p>
							</div>
							);
				} else {
					var refresher = null;
					if (result.hasOwnProperty('delay') && result.delay)
						refresher = <DelayCard mapping={result.mapping}/>
					if (result.hasOwnProperty('message')) {
						window.location.href = result.topath;
							/*
						button = (
							<div id="success">
								<p>{result.message}</p>
								{refresher}
							</div>
							);*/
					}
					else if (result.hasOwnProperty('error_message')) {
						button = (
								<div id="error">
									<p>{result.error_message}</p>
								</div>
									);
					}
				}
			}
		}
				
		return (
				<div id="my-payment-channel">
					<div>
						<button onClick={this.props.onBack}>{trans('ui.btn_back')}</button>
					</div>
					<div id="note">
						{note}
					</div>
					<div>
						<form onSubmit={(e) => this.handlePrepaid(e)}>
							{hidens}
							<div>
								<p>Card serial: </p>
								<input type="text" key="serial" id="serial" name="serial" value={this.state.input.serial} onChange={(e) => this.onSerialChanged(e)}/>
							</div>
							<div>
								<p>Card password: </p>
								<input type="text" key="password" id="password" name="password" value={this.state.input.password} onChange={(e) => this.onPasswordChanged(e)}/>
							</div>
							{button}
						</form>
					</div>
						
				</div>
		);
	}
}