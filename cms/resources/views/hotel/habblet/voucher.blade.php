<ul>
	<li class="even icon-purse">
		<div>You Currently Have:</div>
		<span class="purse-balance-amount">{{ (int) $credits }} Coins</span>
		<div class="purse-tx"><a href="/credits/history">Transactions</a></div>
	</li>
	<li class="odd">
		<div class="box-content">
			<div>Enter voucher:</div>
			<input type="text" name="voucherCode" value="{{ $code }}" id="purse-habblet-redeemcode-string" class="redeemcode" />
			<a href="#" id="purse-redeemcode-button" class="new-button purse-icon" style="float:left"><b><span></span>Redeem</b><i></i></a>
		</div>
	</li>
</ul>
<div id="purse-redeem-result">
<div class="habblet-client-handoff">
<p><b>Redeem this voucher in the hotel</b></p>
<p>This website cannot grant credits or catalogue items. Redeem the code in the hotel.</p>
<p><a href="/client" class="new-button" target="client" onclick="if(typeof HabboClient!='undefined'){HabboClient.openOrFocus(this);} return false;"><b>Open hotel</b><i></i></a></p>
</div>
</div>
