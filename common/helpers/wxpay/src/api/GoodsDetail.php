<?php
namespace wxpay\api;
use wxpay\WxApi;

/**
 *
 */
class GoodsDetail extends WxApi
{
    protected $values;
    private $validProps = [
        'goods_id',
        'wxpay_goods_id',
        'goods_name',
        'quantity',
        'price',
        'goods_category',
        'body'
    ];

    public function getValues(){
        return $this->values;
    }
    public function setGoodsId($value){
        $this->values['goods_id'] = $value;
    }
    public function getGoodsId(){
        return $this->values['goods_id'];
    }
    public function hasGoodsId(){
        return array_key_exists('goods_id', $this->values) && !empty($this->values['goods_id']);
    }
    public function setWxpayGoodsId($value){
        $this->values['wxpay_goods_id'] = $value;
    }
    public function getWxpayGoodsId(){
        return $this->values['wxpay_goods_id'];
    }
    public function hasWxpayGoodsId(){
        return array_key_exists('wxpay_goods_id', $this->values) && !empty($this->values['wxpay_goods_id']);
    }
    public function setGoodsName($value){
        $this->values['goods_name'] = $value;
    }
    public function getGoodsName(){
        return $this->values['goods_name'];
    }
    public function hasGoodsName(){
        return array_key_exists('goods_name', $this->values) && !empty($this->values['goods_name']);
    }
    public function setQuantity($value){
        $this->values['quantity'] = $value;
    }
    public function getQuantity(){
        return $this->values['quantity'];
    }
    public function hasQuantity(){
        return array_key_exists('quantity', $this->values) && !empty($this->values['quantity']);
    }
    public function setPrice($value){
        $this->values['price'] = $value;
    }
    public function getPrice(){
        return $this->values['price'];
    }
    public function hasPrice(){
        return array_key_exists('price', $this->values) && !empty($this->values['price']);
    }
    public function setGoodsCategory($value){
        $this->values['goods_category'] = $value;
    }
    public function getGoodsCategory(){
        return $this->values['goods_category'];
    }
    public function hasGoodsCategory(){
        return array_key_exists('goods_category', $this->values) && !empty($this->values['goods_category']);
    }
    public function setBody($value){
        $this->values['body'] = $value;
    }
    public function getBody(){
        return $this->values['body'];
    }
    public function hasBody(){
        return array_key_exists('body', $this->values) && !empty($this->values['body']);
    }








}
