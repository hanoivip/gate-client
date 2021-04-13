import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import PaymentList from './PaymentList';
export default class Payment extends Component {
	constructor(props) {
	    super(props);
	    this.state = {
	  	      error: null,
	  	      isLoaded: false,
	  	      lang: []
	    }
	}
	componentDidMount1() {
		fetch("/api/topup/lang", {
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
            lang: result.data,
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
    render() {
    	const lang = this.state.lang;
        return (
            <div id="my-payment">
                <PaymentList/>
            </div>
        );
    }
}

if (document.getElementById('my-payment')) {
    ReactDOM.render(<Payment />, document.getElementById('my-payment'));
}
