# Authorize.net AIM Plugin for CakePHP 2.6.x

> **WARNING:** _This plugin is still under development and may not work as expected. Please use with caution and test your implementation thoroughly before putting into production. I do intend to release a v0.1 soon that will include tests and a few more minor tweaks. ~sdevore_

I am starting work on getting this up and running with the 2.6.x core (current work done with 2.6.1) This is forked from https://github.com/egdelwonk/authnet which looks to work at least with 2.2


# origional readme below

# Authorize.net AIM Plugin for CakePHP 1.3.x
### Original Author: Jon Adams (http://github.com/pointlessjon)

**WARNING:** _This plugin is still under development and may not work as expected. Please use with caution and test your implementation thoroughly before putting into production. I do intend to release a v0.1 soon that will include tests and a few more minor tweaks. ~Rick_

## Description

This plugin provides an API to the Authorize.net AIM connection method. It can be used to accept payments by credit card or e-check.


## Installation

Download the plugin and place it in app/plugins/authnet. 

Setup the Authnet datasource by defining it in your database configuration.

	class DATABASE_CONFIG {
		var $authnet = array(
			'datasource' => 'Authnet.AuthnetSource',
			'server' => 'test',	// 'test' or 'live'
			'test_request' => false,
			'login' => 'API_LOGIN_ID',
			'key' => 'API_TRANSACTION_KEY'		
			);
		}


## Setup

This plugin contains a behavior, a model, and a datasource that can all be used together or used separately. It also contains a controller and example views if you want a complete solution.

### Total Solution and Examples

For a complete solution, you can access some of the plugin's built-in functional examples at:

app/authnet/authnet_transactions/add  
app/authnet/authnet_transactions/update  
app/authnet/authnet_transactions/delete  

No database is required if you use this method.

### Attaching to Existing Models

If you need to store the transaction details to a database after processing the Authorize.net transaction there is a behavior to make things easier. It can be attached using the $actsAs array in your existing model.

	var $actsAs = array('Authnet.Authnet');
	
With your database configuration in place, it will automatically authorize payment during validation, and capture payment during save.

#### Special Configuration

To bypass auth during validation set the authDuringValidation field to false.

	var $actsAs = array(
		'Authnet.Authnet' => array(
			'authDuringValidation' => true,
		),
	);

Your model field names can be different than the field names the authnet plugin uses. Simply setup a fields array like the example below for the fields you need to change.

	var $actsAs = array(
		'Authnet.Authnet' => array(
			'fields' => array(
				'card_num' => 'number',
				'exp_date' => 'expiration',
			),
		),
	);


## Security

There are a few security considerations that need to be thought about before implementing this plugin.

- Accepting credit card or e-check payments should only be done over HTTPS.

- In most cases it is against processor terms to store full credit card data. Only store the information you need. It may be enough to store only the transaction id, and/or the last 4 digits of the customer's credit card. If you need the ability to periodically charge the customer's card, consider a solution like [ARB](http://developer.authorize.net/api/arb/) or [CIM](http://developer.authorize.net/api/cim/).  


## More Information

Read more about AIM Implementation from Authorize.net at [http://developer.authorize.net/api/aim/](http://developer.authorize.net/api/aim/).




booya!
