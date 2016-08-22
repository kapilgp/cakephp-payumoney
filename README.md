# cakephp-payumoney
Payumoney Integration Kit for Cakephp2.x

## Set Config
```
  //Payu Settings
  Configure::write('PAYU_MERCHANT_KEY', 'fB7m8s'); //These are test account details, update it with your live account merchant key
  Configure::write('PAYU_MERCHANT_SALT', 'eRis5Chv'); // That is test account salt, update it with your live account salt
```

## To call the Payumoney payment gateway
```
  //PayuMoney Integration
  App::import('Vendor', 'Payu', array('file' => 'payumoney/payu.php'));
  $objPayu = new Payu(Configure::read('PAYU_MERCHANT_KEY'), Configure::read('PAYU_MERCHANT_SALT'), true);
  $txnId = $objPayu->randomTxnId();
  
  //Payu settings
  $payu['txnid'] = $txnId;
  $payu['firstname'] = $this->Auth->User('User.name');
  $payu['email'] = $this->Auth->User('User.email');
  $payu['phone'] = $this->Auth->User('User.mobile');
  
  $payu['productinfo'] = 'Booking - '. $booking['Booking']['code'];
  
  $payu['surl'] = Router::url('/bookings/status/pass/'.$bookingId, true);
  $payu['furl'] = Router::url('/bookings/status/fail/'.$bookingId, true);
  $payu['curl'] = Router::url('/bookings/status/cancel/'.$bookingId, true);
  $payu['amount'] = 10;
  
  //Call Vendor function for send to payu
  $objPayu->send($payu);
```

## License
MIT
