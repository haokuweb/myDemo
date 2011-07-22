<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>数据统计-会员</title>
<link rel="stylesheet" type="text/css" href="http://extjs.cachefly.net/ext-3.3.0/resources/css/ext-all.css">
<script src="http://extjs.cachefly.net/ext-3.3.0/adapter/ext/ext-base.js" language="javascript" charset="UTF-8"></script>
<script src="http://extjs.cachefly.net/ext-3.3.0/ext-all.js" language="javascript" charset="UTF-8"></script>
<script src="http://extjs.cachefly.net/ext-3.3.0/src/locale/ext-lang-zh_CN.js" language="javascript" charset="UTF-8"></script>
<script src="script/combin.js" language="javascript" charset="UTF-8"></script>
<script src="script/client.js" language="javascript" charset="UTF-8"></script>
       <style>
            body {
                margin: 0px;
                padding: 0px;
            }
            body,p,td,th {
                font-size: 10pt;
            }
            img {border:none}
            a{TEXT-DECORATION:none}
            .sxtable{
                border-collapse:collapse;
            }
            .sxtd{
                border:1px solid #999999;
            }
            .rectName{font-family:Arial;text-align:center;font-weight:bold;font-size:12pt;}
            .rectName2{text-align:center;font-weight:bold;font-size:10pt;}
            .codes{text-align:center;font-size:10pt}
            .success-icon {
                background: url(images/success_large.gif) no-repeat;
            }
            .pressed {
                background: url(images/event.gif) no-repeat !important;
            }
            .webHead{
                background-image:url(images/title-bg.gif);
                color:white
            }
            .loading{
                background: url(/images/loading.gif) 0 6px no-repeat !important;
            }
            .stop{
                background: url(/images/star.png) 0 6px no-repeat !important;
            }
            /* [REQUIRED] (by Ext.ux.grid.GridSummary plugin) */
            .x-grid3-gridsummary-row-inner{overflow:hidden;width:100%;background-color:#E0E0E0}/* IE6 requires width:100% for hori. scroll to work */
            .x-grid3-gridsummary-row-offset{width:10000px;}
            .x-grid-hide-gridsummary .x-grid3-gridsummary-row-inner{display:none;}

            td.x-grid3-td-betType{overflow:hidden}
            td.x-grid3-td-betType div.x-grid3-cell-inner{white-space:normal}
            td.x-grid3-td-rate{overflow:hidden}
            td.x-grid3-td-rate div.x-grid3-cell-inner{white-space:normal}
            td.x-grid3-td-cause{overflow:hidden}
            td.x-grid3-td-cause div.x-grid3-cell-inner{white-space:normal}
        </style>
        <title>测试-会员</title>
        <script>
            if (!Ltr.Client.cbBets) Ltr.Client.cbBets={};
            if (!Ltr.panels) Ltr.panels={};
            if (!Ltr.pans) Ltr.pans    ={};
            
            var onliner={"_is_leaf":true,"_parent":"<?php echo $user['parentId']?>","bh":<?php echo $user['bh']?>,"creditSum":<?php echo $user['creditSum']?>,"id":"<?php echo $user['id']?>","lei":<?php echo $user['lei']?>,"leis":<?php echo $user['leis']?>,"maxProrate":<?php echo $user['maxProrate']?>,"name":"<?php echo $user['username']?>","parentProrate":<?php echo $user['parentProrate']?>,"partner":<?php echo $user['partner']?>,"role":<?php echo $user['role']?>,"signId":"<?php echo $user['id']?>","status":<?php echo $user['status']?>};
            
            Ltr.sxYear=<?php echo $site['bmsx'] ?>;
        	Ltr.scTime = <?php echo time()?>000 - new Date().getTime();
        	Ltr.term = <?php echo $site['term'] ?>;
            var logonMsg="<?php echo str_replace("&nbsp;","",$msg)?>";
            var marChangeFlag=-1;
            var mainPanel;
        </script>
    </head>
    <body>
        
    </body>
</html>