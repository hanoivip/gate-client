import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { trans } from './Helpers';
// A type of payment
export default class PaymentType extends Component {
	constructor(props) {
	    super(props);
	    this.state={
	    		type: props.code,
	    		dvalue: 0,
	    };
	  }
	
	handleDvalue(e) {
		var dvalue = e.target.value;
		console.log("Set dvalue" + dvalue);
		this.setState({dvalue: dvalue, type: this.state.type});
		//props will be readonly!!
		//two way databinding not exists
		//this.props.dvalue = dvalue;
	}
	
    render() {
    	var props=this.props;
    	var options = [];
    	if (props.supported_values != null)
	    	Object.keys(props.supported_values).forEach(function (val) {
	    		return options.push(<option value={val}>{props.supported_values[val]}</option>);
	    	});
    	var img = "img/" + props.code + ".png";
    	var imgTag;
    	if (props.enable == "true")
    		imgTag = <img src={img}/>;
    	else
    		imgTag = <img src={img} style={{opacity: 0.3}}/>
    	if (options.length > 0 && props.need_dvalue)
	        return (
	            <div id="my-payment-type">
	            	<p>{props.title}</p><br/>
	            	{imgTag}<br/>
	            	{trans('ui.lbl_choose_value')}: 
	            	<select onChange={(e) => this.handleDvalue(e)} value={this.state.dvalue}>
	            		<option value="0">Select</option>
	            		{options}
            		</select><br/>
	            	<button onClick={() => this.handleClick()}>{trans('ui.btn_next')}</button>
	            </div>
	        );
    	else
    		return (
    				<div id="my-payment-type">
		            	<p>{props.title}</p><br/>
		            	{imgTag}<br/>
		            	<button onClick={() => this.handleClick()}>{trans('ui.btn_next')}</button>
		            </div>
    	        );
    }
    
    handleClick() {
    	var props = this.props;
    	if (!props.available || props.enable=="false")
    		alert(trans('channel-maintain'));
    	else {
    		if (this.props.need_dvalue && this.state.dvalue == 0) {	
    			alert(trans('card-value-empty'));
    			return;
    		}
    		props.onClick(this.state);
    	}	
    }
}