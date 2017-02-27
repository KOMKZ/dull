<tbody>
  <tr>
   <td colspan="6" width="100%"> <p><strong>基本参数</strong></p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>notify_time</p> </td> 
   <td width="10%"> <p>通知时间</p> </td>
   <td width="12%"> <p>Date</p> </td>
   <td width="29%"> <p>通知的发送时间。</p> <p>格式为yyyy-MM-dd HH:mm:ss。</p> </td>
   <td width="11%"> <p>不可空</p> </td>
   <td width="24%"> <p>2009-08-12 11:08:32</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>notify_type</p> </td> 
   <td width="10%"> <p>通知类型</p> </td>
   <td width="12%"> <p>String</p> </td>
   <td width="29%"> <p>通知的类型。</p> </td>
   <td width="11%"> <p>不可空</p> </td>
   <td width="24%"> <p>trade_status_sync</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>notify_id</p> </td> 
   <td width="10%"> <p>通知校验ID</p> </td>
   <td width="12%"> <p>String</p> </td>
   <td width="29%"> <p>通知校验ID。</p> </td>
   <td width="11%"> <p>不可空</p> </td>
   <td width="24%"> <p>70fec0c2730b27528665af4517c27b95</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>sign_type</p> </td> 
   <td width="10%"> <p>签名方式</p> </td>
   <td width="12%"> <p>String</p> </td>
   <td width="29%"> <p>DSA、RSA、MD5三个值可选，必须大写。</p> </td>
   <td width="11%"> <p>不可空</p> </td>
   <td width="24%"> <p>DSA</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>sign</p> </td> 
   <td width="10%"> <p>签名</p> </td>
   <td width="12%"> <p>String</p> </td>
   <td width="29%"> <p>请参见本文档“附录：签名与验签”。</p> </td>
   <td width="11%"> <p>不可空</p> </td>
   <td width="24%"> <p>_p_w_l_h_j0b_gd_aejia7n_ko4_m%2Fu_w_jd3_nx_s_k_mxus9_hoxg_y_r_lunli_pmma29_t_q%3D%</p> <p>3D</p> </td>
  </tr>
  <tr>
   <td colspan="6" width="100%"> <p><strong>业务参数</strong></p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>out_trade_no</p> </td> 
   <td width="10%"> <p>商户网站唯一订单号</p> </td>
   <td width="12%"> <p>String(64)</p> </td>
   <td width="29%"> <p>对应商户网站的订单系统中的唯一订单号，非支付宝交易号。</p> <p>需保证在商户网站中的唯一性。是请求时对应的参数，原样返回。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>3618810634349901</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>subject</p> </td> 
   <td width="10%"> <p>商品名称</p> </td>
   <td width="12%"> <p>String(256)</p> </td>
   <td width="29%"> <p>商品的标题/交易标题/订单标题/订单关键字等。</p> <p>它在支付宝的交易明细中排在第一列，对于财务对账尤为重要。是请求时对应的参数，原样通知回来。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>phone手机</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>payment_type</p> </td> 
   <td width="10%"> <p>支付类型</p> </td>
   <td width="12%"> <p>String(4)</p> </td>
   <td width="29%"> <p>只支持取值为1（商品购买）。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>1</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>trade_no</p> </td> 
   <td width="10%"> <p>支付宝交易号</p> </td>
   <td width="12%"> <p>String(64)</p> </td>
   <td width="29%"> <p>该交易在支付宝系统中的交易流水号。最长64位。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>2014040311001004370000361525</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>trade_status</p> </td> 
   <td width="10%"> <p>交易状态</p> </td>
   <td width="12%"> <p>String</p> </td>
   <td width="29%"> <p>取值范围请参见<a href="#s7">交易状态</a>。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>TRADE_FINISHED</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>gmt_create</p> </td> 
   <td width="10%"> <p>交易创建时间</p> </td>
   <td width="12%"> <p>Date</p> </td>
   <td width="29%"> <p>该笔交易创建的时间。</p> <p>格式为yyyy-MM-dd HH:mm:ss。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>2008-10-22 20:49:31</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>gmt_payment</p> </td> 
   <td width="10%"> <p>交易付款时间</p> </td>
   <td width="12%"> <p>Date</p> </td>
   <td width="29%"> <p>该笔交易的买家付款时间。</p> <p>格式为yyyy-MM-dd HH:mm:ss。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>2008-10-22 20:49:50</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>gmt_close</p> </td> 
   <td width="10%"> <p>交易关闭时间</p> </td>
   <td width="12%"> <p>Date</p> </td>
   <td width="29%"> <p>交易关闭时间。</p> <p>格式为yyyy-MM-dd HH:mm:ss。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>2008-10-22 20:49:46</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>refund_status</p> </td> 
   <td width="10%"> <p>退款状态</p> </td>
   <td width="12%"> <p>String</p> </td>
   <td width="29%"> <p>取值范围请参见<a href="#s8">退款状态</a>。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>REFUND_SUCCESS</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>gmt_refund</p> </td> 
   <td width="10%"> <p>退款时间</p> </td>
   <td width="12%"> <p>Date</p> </td>
   <td width="29%"> <p>卖家退款的时间，退款通知时会发送。</p> <p>格式为yyyy-MM-dd HH:mm:ss。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>2008-10-29 19:38:25</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>seller_email</p> </td> 
   <td width="10%"> <p>卖家支付宝账号</p> </td>
   <td width="12%"> <p>String(100)</p> </td>
   <td width="29%"> <p>卖家支付宝账号，可以是email和手机号码。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>chao.chenc1@alipay.com</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>buyer_email</p> </td> 
   <td width="10%"> <p>买家支付宝账号</p> </td>
   <td width="12%"> <p>String(100)</p> </td>
   <td width="29%"> <p>买家支付宝账号，可以是Email或手机号码。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>13758698870</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>seller_id</p> </td> 
   <td width="10%"> <p>卖家支付宝账户号</p> </td>
   <td width="12%"> <p>String(30)</p> </td>
   <td width="29%"> <p>卖家支付宝账号对应的支付宝唯一用户号。</p> <p>以2088开头的纯16位数字。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>2088002007018916</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>buyer_id</p> </td> 
   <td width="10%"> <p>买家支付宝账户号</p> </td>
   <td width="12%"> <p>String(30)</p> </td>
   <td width="29%"> <p>买家支付宝账号对应的支付宝唯一用户号。</p> <p>以2088开头的纯16位数字。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>2088002007013600</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>price</p> </td> 
   <td width="10%"> <p>商品单价</p> </td>
   <td width="12%"> <p>Number</p> </td>
   <td width="29%"> <p>如果请求时使用的是total_fee，那么price等于total_fee；如果请求时使用的是price，那么对应请求时的price参数，原样通知回来。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>10.00</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>total_fee</p> </td> 
   <td width="10%"> <p>交易金额</p> </td>
   <td width="12%"> <p>Number</p> </td>
   <td width="29%"> <p>该笔订单的总金额。</p> <p>请求时对应的参数，原样通知回来。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>10.00</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>quantity</p> </td> 
   <td width="10%"> <p>购买数量</p> </td>
   <td width="12%"> <p>Number</p> </td>
   <td width="29%"> <p>如果请求时使用的是total_fee，那么quantity等于1；如果请求时使用的是quantity，那么对应请求时的quantity参数，原样通知回来。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>1</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>body</p> </td> 
   <td width="10%"> <p>商品描述</p> </td>
   <td width="12%"> <p>String(1000)</p> </td>
   <td width="29%"> <p>该笔订单的备注、描述、明细等。</p> <p>对应请求时的body参数，原样通知回来。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>Hello</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>discount</p> </td> 
   <td width="10%"> <p>折扣</p> </td>
   <td width="12%"> <p>Number</p> </td>
   <td width="29%"> <p>支付宝系统会把discount的值加到交易金额上，如果需要折扣，本参数为负数。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>-5</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>is_total_fee_adjust</p> </td> 
   <td width="10%"> <p>是否调整总价</p> </td>
   <td width="12%"> <p>String(1)</p> </td>
   <td width="29%"> <p>该交易是否调整过价格。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>N</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>use_coupon</p> </td> 
   <td width="10%"> <p>是否使用红包买家</p> </td>
   <td width="12%"> <p>String(1)</p> </td>
   <td width="29%"> <p>是否在交易过程中使用了红包。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>N</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>extra_common_param</p> </td> 
   <td width="10%"> <p>公用回传参数</p> </td>
   <td width="12%"> <p>String</p> </td>
   <td width="29%"> <p>用于商户回传参数，该值不能包含“=”、“&amp;”等特殊字符。</p> <p>如果用户请求时传递了该参数，则返回给商户时会回传该参数。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>你好，这是测试商户的广告。</p> </td>
  </tr>
  <tr>
   <td width="10%"> <p>business_scene</p> </td> 
   <td width="10%"> <p>是否扫码支付</p> </td>
   <td width="12%"> <p>String</p> </td>
   <td width="29%"> <p>回传给商户此标识为qrpay时，表示对应交易为扫码支付。</p> <p>目前只有qrpay一种回传值。</p> <p>非扫码支付方式下，目前不会返回该参数。</p> </td>
   <td width="11%"> <p>可空</p> </td>
   <td width="24%"> <p>qrpay</p> </td>
  </tr>
 </tbody>
