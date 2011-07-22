<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>数据统计-管理</title>
<link rel="stylesheet" type="text/css" href="http://extjs.cachefly.net/ext-3.3.0/resources/css/ext-all.css">
<link rel="stylesheet" type="text/css" href="css/tab-scroller-menu.css" />
<script src="http://extjs.cachefly.net/ext-3.3.0/adapter/ext/ext-base.js" language="javascript" charset="UTF-8"></script>
<script src="http://extjs.cachefly.net/ext-3.3.0/ext-all.js" language="javascript" charset="UTF-8"></script>
<script src="http://extjs.cachefly.net/ext-3.3.0/src/locale/ext-lang-zh_CN.js" language="javascript" charset="UTF-8"></script>

<script src="script/combin.js" language="javascript" charset="UTF-8"></script>
<script src="script/admin.js" language="javascript" charset="UTF-8"></script>
<style>
.x-form-field-wrap .x-form-spinner-trigger {
	background: transparent url('images/spinner.gif') no-repeat 0 0
}

.x-form-field-wrap .x-form-spinner-overup {
	background-position: -17px 0
}

.x-form-field-wrap .x-form-spinner-clickup {
	background-position: -34px 0
}

.x-form-field-wrap .x-form-spinner-overdown {
	background-position: -51px 0
}

.x-form-field-wrap .x-form-spinner-clickdown {
	background-position: -68px 0
}

.x-trigger-wrap-focus .x-form-spinner-trigger {
	background-position: -85px 0
}

.x-trigger-wrap-focus .x-form-spinner-overup {
	background-position: -102px 0
}

.x-trigger-wrap-focus .x-form-spinner-clickup {
	background-position: -119px 0
}

.x-trigger-wrap-focus .x-form-spinner-overdown {
	background-position: -136px 0
}

.x-trigger-wrap-focus .x-form-spinner-clickdown {
	background-position: -153px 0
}

.x-trigger-wrap-focus .x-form-trigger {
	border-bottom: 1px solid #7eadd9
}

.x-form-field-wrap .x-form-spinner-splitter {
	line-height: 1px;
	font-size: 1px;
	background: transparent url('images/spinner-split.gif') no-repeat 0 0;
	position: absolute;
	cursor: n-resize;
	overflow: hidden
}

.x-trigger-wrap-focus .x-form-spinner-splitter {
	background-position: -14px 0
}

body {
	margin: 0;
	padding: 0
}

body,p,td,th {
	font-size: 12px
}

img {
	border: none
}

a {
	TEXT-DECORATION: none
}

.sxtable {
	border-collapse: collapse
}

.sxtd {
	border: 1px solid #999
}

.rectName {
	font-family: Arial;
	text-align: center;
	font-weight: bold;
	font-size: 12pt
}

.rectName2 {
	text-align: center;
	font-weight: bold;
	font-size: 10pt
}

.rectSum {
	font-size: 8pt
}

.rectRate {
	font-size: 10pt;
	font-weight: bold;
	color: black
}

.rectAdjust {
	font-size: 12pt;
	color: black;
	font-weight: bold
}

.rectDec {
	font-size: 12pt;
	font-weight: bold;
	color: black
}

.codes {
	text-align: center;
	font-size: 10pt
}

.webHead {
	background-image: url(images/title-bg.gif?ver=1);
	color: white
}

.admuser {
	background-image: url(images/user.gif) !important
}

.member {
	background-image: url(images/member.gif) !important
}

.success-icon {
	background: url(images/success_large.gif) no-repeat
}

.x-grid3-gridsummary-row-inner {
	overflow: hidden;
	width: 100%;
	background-color: #e0e0e0
}

.x-grid3-gridsummary-row-offset {
	width: 10000px
}

.x-grid-hide-gridsummary .x-grid3-gridsummary-row-inner {
	display: none
}

td.x-grid3-td-marmsg {
	overflow: hidden
}

td.x-grid3-td-marmsg div.x-grid3-cell-inner {
	white-space: normal
}

td.x-grid3-td-spec {
	overflow: hidden
}

td.x-grid3-td-spec div.x-grid3-cell-inner {
	white-space: normal
}
</style>
<script>
	if (!Ltr.panels)
		Ltr.panels = {};
	if (!Ltr.Ctr.cbBets)
		Ltr.Ctr.cbBets = {};
	if (!Ltr.pans)
		Ltr.pans = {};
	Ltr.sxYear = <?php echo $site['bmsx'] ?>;
	Ltr.scTime = <?php echo time()?>000 - new Date().getTime();
	Ltr.term = <?php echo $site['term'] ?>;;
	var userjs = 'script/user.js?1001';

	var onliner={"_is_leaf":false,"_parent":"<?php echo $user['parentId']?>","bh":<?php echo $user['bh']?>,"creditSum":<?php echo $user['creditSum']?>,"id":"<?php echo $user['id']?>","lei":<?php echo $user['lei']?>,"leis":<?php echo $user['leis']?>,"maxProrate":<?php echo $user['maxProrate']?>,"name":"<?php echo $user['username']?>","parentProrate":<?php echo $user['parentProrate']?>,"partner":<?php echo $user['partner']?>,"role":<?php echo $user['role']?>,"signId":"<?php echo Yii::app()->user->signid?>","status":<?php echo $user['status']?>};
	if (onliner.role > 0)
		onliner.abhFlag = -1;
	onliner.level = <?php echo Yii::app()->user->level?>;

	//操盘手
	//var onliner={"_is_leaf":false,"_parent":null,"bh":2,"creditSum":2000000000,"id":"qq133","lei":0,"leis":7,"maxProrate":1.0,"name":"admin","parentProrate":0.0,"partner":2,"role":0,"signId":"xcf","status":0};
	//onliner.level=1;
	
	//大股东
	//var onliner={"_is_leaf":false,"_parent":"qq133","bh":1,"creditSum":50000,"id":"xcf2","lei":0,"leis":7,"maxProrate":0.85,"name":"新财富","parentProrate":-1.0,"partner":0,"role":1,"signId":"xcf2","status":0};
    //onliner.level=0;
	
	var rebate = 0.135;
	var logonMsg ="<?php echo str_replace("&nbsp;","",$msg)?>";
	var marChangeFlag = -1;
	var countDownEnabled = true;

	var viewport;
	var mainPanel;
	var headPanel;
	var menuPanel;
	var switchPanel;
	var autoCloseTimePanel;
	var statGrid;
	var openClosePanel;
	var nameUsagePanel;
</script>
</head>
<body>
<div style="display:none">
<table id="nameUsageHtml" width="100%">
	<tbody>
		<tr>
			<td><input value="cp" onclick="javascript:Ltr.Ctr.bhcp=1"
				name="nameUsage" type="radio" checked="checked">操盘</td>
		</tr>
		<tr>
			<td><input value="bh" name="nameUsage"
				onclick="javascript:Ltr.Ctr.bhcp=0" type="radio">补货</td>
		</tr>
		<!-- 
		<tr height="3px">
			<td><hr style="margin: 2px 2px 2px 2px"/></td>
		</tr>
		<tr height="18px">
			<td valign="top"><input value="bh" name="nameUsage"
				onclick="javascript:Ltr.Ctr.bhcp=0" checked="checked" type="radio"><b>补货给程序内网</b></td>
		</tr>
		 -->
	</tbody>
</table>

<table id="openCloseTypeHtml" width="100%">
	<tbody>
		<tr>
			<td><input value="3" name="ocType" type="radio">正码类</td>
		</tr>
		<tr>
			<td><input value="1" name="ocType" type="radio">特码类</td>
		</tr>
		<tr>
			<td><input value="0" name="ocType" checked="checked"
				type="radio">程序所有</td>
		</tr>
	</tbody>
</table>
</div>
</body>
</html>