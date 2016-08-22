<?php
/**
 * CakePHP Vendor: PayuMoney
 * @author: Kapil Gupta <kapil.gp@gmail.com>
 * @version: 1.0.0
 * @created: August 20, 2016
 */

class Payu {

    // Merchant key here as provided by Payu
    private $merchantKey = "";
    // Merchant Salt as provided by Payu
    private $salt = "";
    // End point - change to https://secure.payu.in for LIVE mode
    private $payuBaseURL = "";
    //Hash code
    private $hash = "";

    //Constructor
    public function __construct($merchantKey = "", $salt = "", $sandbox = true) {

        $this->merchantKey = $merchantKey;
        $this->salt = $salt;
        $this->payuBaseURL = "https://secure.payu.in";

        if ($sandbox) {
            $this->payuBaseURL = "https://test.payu.in";
        }
    }

    public function getAction() {
        return $this->payuBaseURL . '/_payment';
    }

    public function randomTxnId() {
        // Generate random transaction id
        return substr(hash('sha256', mt_rand() . microtime()), 0, 20);
    }

    private function generateHash($posted = []) {
        // Hash Sequence
        $hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";

        $hashVarsSeq = explode('|', $hashSequence);
        $hash_string = '';
        foreach($hashVarsSeq as $hash_var) {
            $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
            $hash_string .= '|';
        }

        $hash_string .= $this->salt;

        $this->hash = strtolower(hash('sha512', $hash_string));

    }

    public function send($posted = []) {
        // Post Request
        $posted['key'] = $this->merchantKey;
        $this->generateHash($posted);

        $posturl = $this->payuBaseURL . '/_payment';

        $payu_in_args = array(

            // Merchant details
            'key'                   => $this->merchantKey,
            'surl'                  => $posted['surl'],
            'furl'                  => $posted['furl'],
            'curl'                  => $posted['curl'],
            'service_provider'      => 'payu_paisa',

            // Customer details
            'firstname'             => $posted['firstname'],
            'lastname'              => '',
            'email'                 => $posted['email'],
            'address1'              => '',
            'address2'              => '',
            'city'                  => '',
            'state'                 => '',
            'zipcode'               => '',
            'country'               => '',
            'phone'                 => $posted['phone'],

            // Item details
            'productinfo'           => $posted['productinfo'],
            'amount'                => $posted['amount'],

            // Pre-selection of the payment method tab
            'pg'                    => ''

        );

        $payuform = '';

        foreach( $payu_in_args as $key => $value ) {
            if( $value ) {
                $payuform .= "<input type='hidden' name='" . $key . "' value='" . $value . "' />\n";
            }
        }

        $payuform .= '<input type="hidden" name="txnid" value="' . $posted['txnid'] . '" />' . "\n";
        $payuform .= '<input type="hidden" name="hash" value="' . $this->hash . '" />' . "\n";

        // The form
        echo '
          <style>
            body {
                text-lign:      center;
                background-color:#fff;
                cursor: wait;
                margin: 0 auto;
                width: 200px;
            }
            .box {
              margin: 50 0px;
              width: 200px;
              background-color:#e6e6e6;
              padding: 50px;
              border: 3px solid #aaa;
            }
          </style>
          <div class="box">
            <img src="' . Router::url("/img/front/loader.gif", true) . '" alt="Redirecting..." />Thank you for your order. We are now redirecting you to PayUMoney to make payment.
          </div>
          <form action="' . $posturl . '" method="POST" name="payuForm" id="payform">
                ' . $payuform . '
                <input type="submit" class="button" id="submit_payu_in_payment_form" value="Pay via PayUMoney" />
                <a class="button cancel" href="' . $posted['curl'] . '">Cancel order &amp; restore cart</a>
                <script type="text/javascript">
                    var payuForm = document.forms.payuForm;
                    payuForm.submit();
                </script>
            </form>';
        exit;
    }
}
