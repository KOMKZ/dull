<tbody>
  <tr>
   <td width="76">is_success</td>
   <td width="76">成功标识</td>
   <td width="66">String(1)</td>
   <td width="198">表示接口调用是否成功，并不表明业务处理结果。</td>
   <td width="66">不可空</td>
   <td width="104">T</td>
  </tr>
  <tr>
   <td width="76">sign_type</td>
   <td width="76">签名方式</td>
   <td width="66">String</td>
   <td width="198">DSA、RSA、MD5三个值可选，必须大写。</td>
   <td width="66">不可空</td>
   <td width="104">MD5</td>
  </tr>
  <tr>
   <td width="76">sign</td>
   <td width="76">签名</td>
   <td width="66">String(32)</td>
   <td width="198">请参见本文档“附录：签名与验签”。</td>
   <td width="66">不可空</td>
   <td width="104">b1af584504b8e845ebe40b8e0e733729</td>
  </tr>
  <tr>
   <td width="76">out_trade_no</td>
   <td width="76">商户网站唯一订单号</td>
   <td width="66">String(64)</td>
   <td width="198">对应商户网站的订单系统中的唯一订单号，非支付宝交易号。需保证在商户网站中的唯一性。是请求时对应的参数，原样返回。</td>
   <td width="66">可空</td>
   <td width="104">6402757654153618</td>
  </tr>
  <tr>
   <td width="76">subject</td>
   <td width="76">商品名称</td>
   <td width="66">String(256)</td>
   <td width="198">商品的标题/交易标题/订单标题/订单关键字等。</td>
   <td width="66">可空</td>
   <td width="104">手套</td>
  </tr>
  <tr>
   <td width="76">payment_type</td>
   <td width="76">支付类型</td>
   <td width="66">String(4)</td>
   <td width="198">只支持取值为1（商品购买）。</td>
   <td width="66">可空</td>
   <td width="104">1</td>
  </tr>
  <tr>
   <td width="76">exterface</td>
   <td width="76">接口名称</td>
   <td width="66">String</td>
   <td width="198">标志调用哪个接口返回的链接。</td>
   <td width="66">可空</td>
   <td width="104">create_direct_pay_by_user</td>
  </tr>
  <tr>
   <td width="76">trade_no</td>
   <td width="76">支付宝交易号</td>
   <td width="66">String(64)</td>
   <td width="198">该交易在支付宝系统中的交易流水号。最长64位。</td>
   <td width="66">可空</td>
   <td width="104">2014040311001004370000361525</td>
  </tr>
  <tr>
   <td width="76">trade_status</td>
   <td width="76">交易状态</td>
   <td width="66">String</td>
   <td width="198"> <p>交易目前所处的状态。成功状态的值只有两个：</p>
    <ul>
     <li><span style="font-size: 10pt;">TRADE_FINISHED（普通即时到账的交易成功状态）；</span></li>
     <li><span style="font-size: 10pt;">TRADE_SUCCESS（开通了高级即时到账或机票分销产品后的交易成功状态）</span></li>
    </ul> </td>
   <td width="66">可空</td>
   <td width="104">TRADE_FINISHED</td>
  </tr>
  <tr>
   <td width="76">notify_id</td>
   <td width="76">通知校验ID</td>
   <td width="66">String</td>
   <td width="198">支付宝通知校验ID，商户可以用这个流水号询问支付宝该条通知的合法性。</td>
   <td width="66">可空</td>
   <td width="104">RqPnCoPT3K9%2Fvwbh3I%2BODmZS9o4qChHwPWbaS7UMBJpUnBJlzg42y9A8gQlzU6m3fOhG</td>
  </tr>
  <tr>
   <td width="76">notify_time</td>
   <td width="76">通知时间</td>
   <td width="66">Date</td>
   <td width="198">通知时间（支付宝时间）。格式为yyyy-MM-dd HH:mm:ss。</td>
   <td width="66">可空</td>
   <td width="104">2008-10-23 13:17:39</td>
  </tr>
  <tr>
   <td width="76">notify_type</td>
   <td width="76">通知类型</td>
   <td width="66">String</td>
   <td width="198">返回通知类型。</td>
   <td width="66">可空</td>
   <td width="104">trade_status_sync</td>
  </tr>
  <tr>
   <td width="76">seller_email</td>
   <td width="76">卖家支付宝账号</td>
   <td width="66">String(100)</td>
   <td width="198">卖家支付宝账号，可以是Email或手机号码。</td>
   <td width="66">可空</td>
   <td width="104">chao.chenc1@alipay.com</td>
  </tr>
  <tr>
   <td width="76">buyer_email</td>
   <td width="76">买家支付宝账号</td>
   <td width="66">String(100)</td>
   <td width="198">买家支付宝账号，可以是Email或手机号码。</td>
   <td width="66">可空</td>
   <td width="104">tstable01@alipay.com</td>
  </tr>
  <tr>
   <td width="76">seller_id</td>
   <td width="76">卖家支付宝账户号</td>
   <td width="66">String(30)</td>
   <td width="198">卖家支付宝账号对应的支付宝唯一用户号。以2088开头的纯16位数字。</td>
   <td width="66">可空</td>
   <td width="104">2088002007018916</td>
  </tr>
  <tr>
   <td width="76">buyer_id</td>
   <td width="76">买家支付宝账户号</td>
   <td width="66">String(30)</td>
   <td width="198">买家支付宝账号对应的支付宝唯一用户号。以2088开头的纯16位数字。</td>
   <td width="66">可空</td>
   <td width="104">2088101000082594</td>
  </tr>
  <tr>
   <td width="76">total_fee</td>
   <td width="76">交易金额</td>
   <td width="66">Number</td>
   <td width="198">该笔订单的资金总额，单位为RMB-Yuan。取值范围为[0.01,100000000.00]，精确到小数点后两位。</td>
   <td width="66">可空</td>
   <td width="104">10.00</td>
  </tr>
  <tr>
   <td width="76">body</td>
   <td width="76">商品描述</td>
   <td width="66">String(1000)</td>
   <td width="198">对一笔交易的具体描述信息。如果是多种商品，请将商品描述字符串累加传给body。</td>
   <td width="66">可空</td>
   <td width="104">Hello</td>
  </tr>
  <tr>
   <td width="76">extra_common_param</td>
   <td width="76">公用回传参数</td>
   <td width="66">String</td>
   <td width="198">用于商户回传参数，该值不能包含“=”、“&amp;”等特殊字符。如果用户请求时传递了该参数，则返回给商户时会回传该参数。</td>
   <td width="66">可空</td>
   <td width="104">你好，这是测试商户的广告。</td>
  </tr>
 </tbody>
