<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MailText
 *
 * @author peto
 */
class MailContent {
    
    
    protected static $NEW_ORDER = 1;
    
    protected static $ACCEPTED_ORDER = 2;
    
    protected static $SENDED_ORDER = 3;
    
    protected static $FINISHED_ORDER = 4;
    
    protected static $CANCLED_ORDER = 5;
    
    private $databaseConn = null;
    
    private $subject = "";
    
    private $body = "";
    
    private $orderDetail = array();
    
    
    
    public function __construct($databaseConn, $orderId){
          
        if(! $databaseConn instanceof Database){
            throw new Exception("Database connection is not set.");
        }     
        
        if(!is_numeric($orderId)){
            throw new Exception("Order ID ". $orderId . " is invalid.");
        }
        
        $this->databaseConn = $databaseConn;
        
        $this->getOrderById( $orderId );

    }
    
    
    
    /**
     * Select user defined text form database and append to content
     * @throws MysqlException, if database query failed.
     */
    public function appendDefinedText(){   
        $result = $this->databaseConn->select("SELECT `val` FROM `shop_config_text` WHERE `id_shop_config_text`=? LIMIT 1", 
                  array( $this->orderDetail['id_shop_order_status']) );
        $this->body .= $result[0]["val"];
    }
    
    
    /**
     * Create and format customer info to email body as html.
     * 
     * @throws MysqlException if computing total price of order filed
     * 
     */
    public function appendCustomerInfo(){
        
        $this->setTotalPriceOfOrder();
        
        $orderWithDPH = $this->withDPH(  $this->computeSale($this->orderDetail['total_price'] ) );
        $deliveryWithDPH =  $this->orderDetail['delivery_dph'] == 0 ? 
                        $this->orderDetail['price_delivery'] : $this->withDPH($this->orderDetail['price_delivery']);
                        
                        
        $this->body .= "<br>== Informácie o objedávke ===================================<br>".
                          "<b>Spôsob platby:</b> ".$this->orderDetail['payment_name']."<br> ".
                          "<b>Spôsob dopravy:</b> ".$this->orderDetail['delivery_name']."<br> ".
                          "<b>Cena objednávky s ".$this->orderDetail['dph']."% DPH:</b> ".  
                            number_format($this->orderDetail['total_price_dph'], 2)." ".$this->orderDetail['shop_currency_name']."<br>".
                         "<b>Cena dobravy: </b> ".  
                            number_format($deliveryWithDPH, 2)." ".$this->orderDetail['shop_currency_name']."<br>".
                "<b>Celkom k úhrade:</b> ".  
                            number_format($this->orderDetail['total_price_dph'] + $deliveryWithDPH, 2)." ".$this->orderDetail['shop_currency_name']." <br>";
                        
        $this->body .= "<br>== Informácie o zákazníkovi ==================================<br>";
        if(strlen($this->orderDetail['company']) > 1){
            $this->body .= "<b>Firma:</b> ".$this->orderDetail['company']."<br>".
                            "<b>IČO:</b> ".$this->orderDetail['ico']."<br>".
                            "<b>DIČ:</b> ".$this->orderDetail['dic']."<br>";
            
        }
        $this->body .= "<b>Meno:</b> ".$this->orderDetail['givenname']." ".$this->orderDetail['surname']."<br>".
                          "<b>Adresa:</b> "
                                   .$this->orderDetail['street'].", "
                                  .$this->orderDetail['city'].", "
                                  .$this->orderDetail['zip']."<br>".
                          "<b>Telefón:</b> +421" .$this->orderDetail['mobil']."<br>".
                          "<b>Email:</b> " .$this->orderDetail['mail'];
       if(strlen($this->orderDetail['d_city']) > 1 && 
          strlen($this->orderDetail['d_surname']) > 1 && 
          strlen($this->orderDetail['d_zip']) > 1){
           $this->body .= "<br><br>== Dodacia adresa =====================================<br>";
           if(strlen($this->orderDetail['d_company']) > 1){
                $this->body .= 
                            (isset($this->orderDetail['company']) ? "<b>Firma:</b> ".$this->orderDetail['company']."<br>" : '').
                            (isset($this->orderDetail['d_ico']) ? "<b>IČO:</b> ".$this->orderDetail['d_ico']."<br>" : '').
                            (isset($this->orderDetail['d_dic']) ? "<b>DIČ:</b> ".$this->orderDetail['d_dic']."<br>" : '');
             
          }
          $this->body .= "<b>Meno:</b> ".$this->orderDetail['d_givenname']." ".$this->orderDetail['d_surname']."<br>";
                          "<b>Adresa:</b> "
                                  .$this->orderDetail['d_street'].", "
                                  .$this->orderDetail['d_city'].", "
                                  .$this->orderDetail['d_zip']."<br>".
                          "<b>Telefón:</b> " .$this->orderDetail['d_mobil'];
          
          }
    }
    
    
    
    /**
     * Append order items to mail content
     * 
     * 
     * @throws MysqlExceprion
     */
    public function appendOrderItems(){
        $data = $this->getOrderItems($this->orderDetail['id_shop_order'], NULL);
        $orderWithDPH = $this->withDPH(  $this->computeSale($this->orderDetail['total_price'] ) );
        $deliveryWithDPH =  $this->orderDetail['delivery_dph'] == 0 ? 
                        $this->orderDetail['price_delivery'] : $this->withDPH($this->orderDetail['price_delivery']);
        
        $this->body .= "<br><br>== Položky objednávky =======================================<br>";

	for($i = 0; $i < count($data); $i++ ){
            $this->body .= ($data[$i]['shop_manufacturer_name'] != "Nepriradený" ? 
                    $data[$i]['shop_manufacturer_name']." / "  : '' ).$data[$i]['title_sk'].', '
                    .(intval($data[$i]['id_shop_variant']) != 0 ? $this->getVariantName($data[$i]['id_shop_variant'])  : '-' ).", "
                    .$data[$i]['count'].' ks, '
                    .number_format( $this->withDPH($data[$i]['price']) * $data[$i]['count'], 2 ).' '
                    .$this->orderDetail['shop_currency_name'].' s DPH<br>';
        }
        $this->body .= "Doprava tovaru: ".$this->orderDetail['delivery_name'].", ".number_format($deliveryWithDPH,2)
                    ." ".$this->orderDetail['shop_currency_name'].' s DPH<br>'.
                    "==========================================================<br>".
                    "<h3>Celkom k úhrade: ".number_format($this->orderDetail['total_price_dph']  + $deliveryWithDPH,2)." ".$this->orderDetail['shop_currency_name']."</h3>";
    }
    
    
    
    /**
     * Select all data of given order
     * 
     * @throws MysqlException if an error occured
     * @throws Exception if Order not found
     * @param type $id ID of order
     */
    public function getOrderById($id){
        $data = $this->databaseConn->select(
            "SELECT o.`id_shop_order`, o.`id_shop_delivery`,  o.`id_user`, o.`id_shop_payment`, 
                    o.`id_shop_order_status`, o.`create`, o.`edit`, o.`editor`, o.`ip`, o.`total_price`, 
                    o.`dph`, o.`givenname`, o.`surname`, o.`mobil`, o.`street`, o.`city`, o.`zip`, 
                    o.`company`, o.`ico`, o.`dic`, o.`d_givenname`, o.`d_surname`, o.`d_company`, 
                    o.`d_mobil`,  o.`d_street`, o.`d_city`, o.`d_zip`,o.`sale`, o.`mail`, p.`payment_name`,
                    o.`price_delivery`, o.`note`, cu.`shop_currency_name`, d.`dph` as delivery_dph, d.`delivery_name`
            FROM  `shop_order` o, `shop_order_status` s, `shop_currency` cu, `shop_delivery` d, `shop_payment` p
            WHERE  o.`id_shop_order_status`=s.`id_shop_order_status` AND d.`id_shop_delivery`=o.`id_shop_delivery` 
                     AND p.`id_shop_payment`=o.`id_shop_payment` AND o.`id_shop_order`=? LIMIT 1", 
         array( $id ));
        
        if($data == null || !is_array($data[0])){
            throw new Exception("Order ID: ". $id . " does not exists.");
        }
        $this->orderDetail = $data[0];
	
    }
    
    
    public function getOrderItems(){
        return $this->databaseConn->select(
                "SELECT i.`id_shop_item`, i.`id_shop_product`, i.`price`, i.`count`, i.`id_shop_variant`, 
                        p.`title_sk`, p.`ean`, m.`shop_manufacturer_name`
		FROM `shop_item` i, `shop_product` p, `shop_manufacturer` m
		WHERE i.`id_shop_product`=p.`id_shop_product` AND p.`id_shop_manufacturer`=m.`id_shop_manufacturer` 
                      AND i.`id_shop_order`=?", array( $this->orderDetail['id_shop_order'] ));
	
    }
    
    
    function getVariantName($id){
	global $conn;
	$data = $conn->select("SELECT `shop_variant_name` FROM `shop_variant` WHERE `id_shop_variant`=? LIMIT 1", array( $id ));
	return $data[0]['shop_variant_name'];
    }

    
    public function getOrderId() {
        return $this->orderId;
    }
    
    public function getCustomerMail() {
        return $this->orderDetail['mail'];
    }

    public function setOrderId($orderId) {
        $this->orderId = $orderId;
    }
    
    public function getSubject() {
        return $this->subject;
    }

    public function getBody() {
        return $this->body;
    }
    
    function computeSale($val){
        return $val * (1 - $this->orderDetail['sale'] / 100);
    }
    
    function withDPH($val){
        return round($val * (1 + $this->orderDetail['dph'] / 100),2);
    }
    
    public function setOrderStatus($statusId){
        $this->orderDetail['id_shop_order_status'] = (int)$statusId;
    }
    
    /**
     * Comute total price of order and data set in $this->orderDetail['total_price']
     * 
     * @throws MysqlException if computing failed.
     */
    public function setTotalPriceOfOrder(){
        $dph = (1 + $this->orderDetail['dph'] / 100);
        $data = $this->databaseConn->select("SELECT SUM(`price` * `count`) as sum, SUM(ROUND(`price` * $dph, 2) * `count`) as sum_dph FROM `shop_item` WHERE `id_shop_order`=?", 
                array(  $this->orderDetail['id_shop_order'] ));
        $this->orderDetail['total_price'] = $data[0]['sum'];
        $this->orderDetail['total_price_dph'] = $data[0]['sum_dph'];
    }
    
    
    /**
     * This method generate text of order,
     * 
     * 
     * if is status New order append all posible texts
     * @throws MysqlException if some db query failed.
     * @throws Exception if, status is unknown.
     */
    public function generateMailContent(){
        
        $this->subject = "Informácie o objednávke: ".  $this->orderDetail["id_shop_order"]; 
        switch ((int)$this->orderDetail["id_shop_order_status"]) {
            

           case self::$ACCEPTED_ORDER:
           case self::$FINISHED_ORDER:
           case self::$SENDED_ORDER:
                $this->appendDefinedText();
                $this->appendCustomerInfo();
                break;
           case self::$NEW_ORDER:
                $this->appendDefinedText();
                $this->appendCustomerInfo();
                $this->appendOrderItems();
                break;
           case self::$CANCLED_ORDER:
                $this->appendDefinedText();
                $this->subject = "Objednávka ".$this->orderDetail["id_shop_order"]." bola zrušená";
                break;
            default:
                throw new Exception("Invalid key of MailText");
                break;
        }
    }
    
}

?>
