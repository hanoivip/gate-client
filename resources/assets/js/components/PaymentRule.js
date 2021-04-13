import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import Modal from 'react-awesome-modal';
import { trans } from './Helpers';
export default class PaymentRule extends Component {
	constructor(props) {
		super(props);
		this.state = {
				error: null,
				show: false,
				ruleHtml: null,
		};
		this.closeRule = this.closeRule.bind(this);
		this.viewRule = this.viewRule.bind(this);
	}
	componentDidMount() {
		var showOnLoad = false;
		if (this.props.hasOwnProperty('showOnLoad'))
			showOnLoad = this.props.showOnLoad;
		axios.get('/api/topup/rule')
		.then((resp) => {
			this.setState({
				show: showOnLoad,
				ruleHtml: resp.data.html,
			});
			if (showOnLoad)
				this.viewRule();
		})
		.catch((ex) => {
			this.setState({
				error: ex
			});
		});
	}
	closeRule() {
		this.setState({
			show: false,
		});
	}
	viewRule() {
		this.setState({
			show: true,
		});
	}
    render() {
        const show = this.state.show;
        const error = this.state.error;
        if (this.state.ruleHtml == null) {
        	return (<div id="my-payment-rule"><img src="img/loading.gif"/></div>);
        }
        return (<div id="my-payment-rule">
        			<a onClick={this.viewRule}>{trans('ui.txt_rule')}</a>
        			<Modal 
	                    visible={this.state.show}
	                    width="60%"
	                    height="60%"
	                    effect="fadeInUp"
	                    onClickAway={() => this.closeRule()}>
	                    <div id="my-payment-rule-model">
	                        <h1>{trans('ui.lbl_rule_title')}:</h1>
	                        <div dangerouslySetInnerHTML={{ __html: this.state.ruleHtml }}></div>
	                        <div>
	                        	<a href="javascript:void(0);" onClick={() => this.closeRule()}>{trans('ui.btn_close')}</a>
	                        </div>
	                    </div>
	                </Modal>
        		</div>);
    }
}

if (document.getElementById('my-payment-rule')) {
    ReactDOM.render(<PaymentRule />, document.getElementById('my-payment-rule'));
}
