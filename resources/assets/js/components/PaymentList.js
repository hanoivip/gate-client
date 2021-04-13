import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import PaymentType from './PaymentType';
import PaymentChannel from './PaymentChannel';
import PaymentRule from './PaymentRule';
import { trans } from './Helpers';
// List all of support payment types
export default class PaymentList extends Component {
	
	constructor(props) {
	    super(props);
	    // this.state is special variable
	    // this.setState is function to switch this variable'
	    // modify state will trigger component updates
	    this.state = {
	      error: null,
	      isLoaded: false,
	      types: [],
	      enabled: [],
	      cutoffs: [],
	      selected: {
	  	    	type: null,
		    	dvalue: 0,
		    },
	    };
	  }
	
  componentDidMount() {
	    fetch("/api/topup?access_token=" + API_TOKEN, {
	    	method: 'GET',
	    	headers: {
	    		'X-Requested-With': 'XMLHttpRequest',
	    	}
	    })
      .then(res => res.json())
      .then(
        (result) => {
          this.setState({
            isLoaded: true,
            types: result.cardtypes,
            enabled: result.enabled,
            cutoffs: result.cutoffs,
          });
        },
        (error) => {
          this.setState({
            isLoaded: true,
            error: error
          });
        }
      )
  }
  
  handleSelectType(selected) {
	  console.log("User select type:" + selected.type + " " + selected.dvalue);
	  var state = this.state;
	  state.selected = selected;
	  this.setState(state);
  }
  
  handleReset() {
	  var state = this.state;
	  state.selected = {
			  type: null,
			  dvalue: 0,
		  };
	  this.setState(state);
  }
  
  renderChannel() {
	  const selected = this.state.selected;
	  const types = this.state.types;
	  return <PaymentChannel key={selected.type} type={selected.type} dvalue={selected.dvalue} need_dvalue={types[selected.type].need_dvalue} onBack={() => this.handleReset()}/>;
  }
  
  renderList() {
	  const {error, isLoaded, types, enabled, cutoffs, selected} = this.state;
	  //console.log(enabled);
	  var list=[];
	  //https://stackoverflow.com/questions/29517715/react-this-state-disappears-in-for-loop
Object.keys(types).forEach((type) => {
	//console.log(enabled.includes(type));
	if (enabled.includes(type))
		list.push(<PaymentType key={type} code={type} title={types[type].title} need_dvalue={types[type].need_dvalue} supported_values={types[type].supported_values} available={types[type].available} enable="true" onClick={(s) => this.handleSelectType(s)}/>);
	else
		list.push(<PaymentType key={type} code={type} title={types[type].title} need_dvalue={types[type].need_dvalue} supported_values={types[type].supported_values} available={types[type].available} enable="false" onClick={(s) => this.handleSelectType(s)}/>);
});
if (list.length > 0)
  	return (
  			<div id="my-payment-list">  				
		      {list}
		      <PaymentRule showOnLoad="true"/>
  			</div>
  			);
else
	return <div id="my-payment-list"><p>{trans('all-unavaiable')}</p></div>;
  }
  
  renderLoading() {
	  return <div id="my-payment-list"><img src="img/loading.gif"/></div>;
  }
	
	render() {
		const selected = this.state.selected;
		console.log(selected.type);
		if (selected.type != null)
			return this.renderChannel();
		const {error, isLoaded, types} = this.state;
	    if (isLoaded) {
	    	return this.renderList();
	    } else {
	    	return this.renderLoading();
	    }
	}
}
