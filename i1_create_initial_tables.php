<?php
// *******************************
// ******** SELECT HOST **********
// *******************************
  		// HOSTBASKET
//		$settings['dbhost']= 'sql17.hostbasket.com';
//		$settings['dbusername']= 'EMPTY';
//		$settings['dbname']= 'EMPTY';
//		$settings['dbpassword']= '7840de38';

		// OFFICE VINFOTECH TESTSERVER
	$settings['dbhost']= 'SQL-SERVER';
	$settings['dbusername']= 'indigo';
    $settings['dbname']= 'basf2';
	$settings['dbpassword']= '7840de38';

// *******************************
// ********  CONNECT *************
// *******************************
if($conn = mssql_connect($settings['dbhost'],$settings['dbusername'],$settings['dbpassword']))
	echo "<font color=green size=1>CONNECTED: ".$settings['dbhost']." | " . $settings['dbname']." </font> <br />";
else die ("Can't connect to ".$settings['dbhost']." | " . $settings['dbname']);
	mssql_select_db($settings['dbname']);

// *******************************
// ******  CALL FUNCTIONS  *******
// *******************************

//create_initial_order_log('order_log');
//create_initial_indigo_config('indigo_config');
//create_initial_indigo_config_cat('indigo_config_cat');
//create_initial_indigo_user('indigo_user');
//create_initial_indigo_tags('indigo_tags_IPAD');
//create_initial_indigo_tags('indigo_tags_CUST');
//create_initial_indigo_tags('indigo_tags_DEVELOPER');
//create_initial_indigo_tags('indigo_tags_REPORT');
//create_initial_indigo_tags('indigo_tags_TPT');
//create_initial_indigo_tags('indigo_tags');

die('disable die() from the script!!!!');

//create_initial_order_log('order_log');
//create_initial_indigo_user('indigo_user');



//if (create_initial_table_exists('tpto_in')) mssql_query("DROP TABLE [dbo].tpto_in");
//create_initial_tpto_order('tpto_in','vend');
//create_initial_tpto_order('tpto_out','cust');
//create_initial_tpto_order('tpto_in_history','vend');
//create_initial_tpto_order('tpto_out_history','cust');
//create_initial_tpto_order('tpto_inter','cust');
//create_initial_tpto_order('tpto_inter_history','cust');

//create_initial_tpto_forw('tpto_forw');
//create_initial_tpto_prodgroup('tpto_prodgroup');
//crate_initial_tpto_prod('tpto_prod');
//create_initial_tpto_cust('tpto_cust');

//create_initial_indigo_tags('indigo_tags_TPT');
//create_initial_indigo_tags('indigo_tags_BAS');
//create_initial_indigo_tags('indigo_tags_DEV');
//create_initial_indigo_tags('indigo_tags');
//create_initial_indigo_user('indigo_user');
//create_initial_indigo_accessgroup('indigo_accessgroup');
//create_initial_indigo_language('indigo_language');
//create_initial_indigo_status('indigo_status');
//create_initial_indigo_config('indigo_config');
//create_initial_indigo_customer('indigo_customer');
//create_initial_tpto_closeout('tpto_closeout');
//create_initial_tpto_storage('tpto_storage');

//create_initial_tpto_country('tpto_country');
//create_initial_tpto_prodgroup('tpto_prodgroup');
//create_initial_tpto_prod('tpto_prod');

//create_initial_tpto_cust('tpto_vend');
//create_initial_tpto_site('tpto_site');
//create_initial_tpto_forw('tpto_forw');
//create_initial_tpto_country('tpto_country');

//create_initial_tpto_order_template('tpto_order_template');
//create_initial_tpto_order('tpto_in','vend');
//create_initial_tpto_order('tpto_out','cust');
//create_initial_tpto_order('tpto_in_history','vend');
//create_initial_tpto_order('tpto_out_history','cust');
//create_initial_tpto_order('tpto_inter','cust');
//create_initial_tpto_order('tpto_inter_history','cust');


//die('disable die() from the script!!!!');
die("ok ok ok ...done!!");


Function create_initial_order_log($t)
{
$Astruct=array(
array('col'=>'[id]',			'type'=>'[int]',		'param'=>'IDENTITY(1,1) NOT NULL','constr'=>'',	'def'=>"" ),
array('col'=>'[log_tag]',		'type'=>'[varchar](24)','param'=>'NULL',		'constr'=>'',	'def'=>" DEFAULT('') " ),
array('col'=>'[tpt_ordernr]',	'type'=>'[varchar](16)','param'=>'NOT NULL',		'constr'=>'',	'def'=>" DEFAULT('TPT-in-090001') " ),
array('col'=>'[cust_ordernr]',	'type'=>'[varchar](16)','param'=>'NOT NULL',			'constr'=>'',	'def'=>" DEFAULT('090001') " ),
array('col'=>'[log_time]',		'type'=>'[datetime]',	'param'=>'NOT NULL',				'constr'=>'',	'def'=>"" ),
array('col'=>'[log_tcpip]',		'type'=>'[varchar](24)','param'=>'NOT NULL',		'constr'=>'',	'def'=>" DEFAULT('') " ),
array('col'=>'[indigo_user_id]','type'=>'[int]',		'param'=>'NULL', 				'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[action]',		'type'=>'[varchar](24)','param'=>'NOT NULL',			'constr'=>'',	'def'=>" DEFAULT('forward_pending') " ),
array('col'=>'[description]',	'type'=>'[text]',		'param'=>'NULL',				'constr'=>'',	'def'=>" DEFAULT('') " ),
array('col'=>'[isactive]',		'type'=>'[bit]',		'param'=>'NULL', 				'constr'=>'',	'def'=>" DEFAULT(1) " ),
array('col'=>'[islocked]',		'type'=>'[bit]',		'param'=>'NULL', 				'constr'=>'',	'def'=>" DEFAULT(0) " )
);

$Ainsert=array(
//array('identifier'=>'CALCIUMFORMI',	'naam'=>'Calciumformiaat'),
);
echo create_initial_create_table($t, $Astruct, 'ON [PRIMARY]');
echo create_initial_insert_records($t, $Ainsert);
}



Function create_initial_tpto_prodgroup($t)
{
$Astruct=array(
array('col'=>'[id]',		'type'=>'[int]',		'param'=>'IDENTITY(1,1) NOT NULL',	'constr'=>'','def'=>"" ),
array('col'=>'[identifier]','type'=>'[varchar](12)','param'=>'UNIQUE NOT NULL',				'constr'=>'','def'=>" DEFAULT('PRODGROUP') " ),
array('col'=>'[naam]',		'type'=>'[varchar](24)','param'=>'NOT NULL',		'constr'=>'',		'def'=>" DEFAULT('Productgroup') " ),
array('col'=>'[comment]',	'type'=>'[text]',		'param'=>'NULL',			'constr'=>'',		'def'=>" DEFAULT('') " ),
array('col'=>'[isactive]',	'type'=>'[bit]',		'param'=>'NULL', 			'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[islocked]',	'type'=>'[bit]',		'param'=>'NULL', 			'constr'=>'',		'def'=>" DEFAULT(0) " )
);

$Ainsert=array(
array('identifier'=>'FCC',	'naam'=>'Fluid Cracking Catalyst'),
array('identifier'=>'ECAT',	'naam'=>'Equilibrium Catalyst'),
array('identifier'=>'ADDITIVES',	'naam'=>'Additives')
);
echo create_initial_create_table($t, $Astruct, 'ON [PRIMARY]');
echo create_initial_insert_records($t, $Ainsert);
}

Function create_initial_tpto_prod($t)
{
$Astruct=array(
array('col'=>'[id]',		'type'=>'[int]',		'param'=>'IDENTITY(1,1) NOT NULL',	'constr'=>'','def'=>"" ),
array('col'=>'[identifier]','type'=>'[varchar](12)','param'=>'UNIQUE NOT NULL',			'constr'=>'','def'=>" DEFAULT('PRODNAME') " ),
array('col'=>'[naam]',		'type'=>'[varchar](64)','param'=>'NOT NULL',		'constr'=>'',		'def'=>" DEFAULT('Productname') " ),
//array('col'=>'[product]',	'type'=>'[varchar](64)','param'=>'NOT NULL',		'constr'=>'',		'def'=>" DEFAULT('productnaam') " ),
array('col'=>'[tpto_prodgroup_id]',	'type'=>'[int]','param'=>'NOT NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[purity]',	'type'=>'[decimal](9,2)','param'=>'NULL',		'constr'=>'',		'def'=>"DEFAULT(100)" ),
array('col'=>'[density]',	'type'=>'[decimal](9,2)','param'=>'NOT NULL',		'constr'=>'',		'def'=>"DEFAULT(1)" ),
array('col'=>'[uncode]',	'type'=>'[int]','param'=>'NOT NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[un_nl]',		'type'=>'[varchar](64)','param'=>'NOT NULL',		'constr'=>'',		'def'=>" DEFAULT('UN') " ),
array('col'=>'[un_fr]',		'type'=>'[varchar](64)','param'=>'NOT NULL',		'constr'=>'',		'def'=>" DEFAULT('UN') " ),
array('col'=>'[un_uk]',		'type'=>'[varchar](64)','param'=>'NOT NULL',		'constr'=>'',		'def'=>" DEFAULT('UN') " ),
array('col'=>'[un_de]',		'type'=>'[varchar](64)','param'=>'NOT NULL',		'constr'=>'',		'def'=>" DEFAULT('UN') " ),
array('col'=>'[isbaseproduct]',	'type'=>'[bit]',	'param'=>'NULL', 			'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[baseprod_id]',	'type'=>'[int]','param'=>'NOT NULL',		'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[mix1prod_id]',	'type'=>'[int]','param'=>'NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[mix2prod_id]',	'type'=>'[int]','param'=>'NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[mix3prod_id]',	'type'=>'[int]','param'=>'NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[balanceprod_id]','type'=>'[int]','param'=>'NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[basepercentage]','type'=>'[decimal](9,2)','param'=>'NOT NULL',		'constr'=>'',		'def'=>"DEFAULT(100)" ),
array('col'=>'[mix1percentage]','type'=>'[decimal](9,2)','param'=>'NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[mix2percentage]','type'=>'[decimal](9,2)','param'=>'NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[mix3percentage]','type'=>'[decimal](9,2)','param'=>'NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[balancepercentage]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),

array('col'=>'[comment]',		'type'=>'[text]',	'param'=>'NULL',			'constr'=>'',		'def'=>" DEFAULT('') " ),
array('col'=>'[isactive]',		'type'=>'[bit]',	'param'=>'NULL', 			'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[islocked]',		'type'=>'[bit]',	'param'=>'NULL', 			'constr'=>'',		'def'=>" DEFAULT(0) " )
);

$Ainsert=array(
array('isbaseproduct'=>1,'baseprod_id'=>3,'balanceprod_id'=>1,'identifier'=>'FCC','naam'=>'FLUID CRACKING CATALYST','tpto_prodgroup_id'=>1,'purity'=>100,'density'=>0.90,'uncode'=>0,'un_nl'=>'FLUID CRACKING CATALYST','un_fr'=>'FLUID CRACKING CATALYST','un_uk'=>'STATISTIC NR. 3815 9090','un_de'=>'MANUFACTURING COMPANY: basf2','basepercentage'=>100),
array('isbaseproduct'=>1,'baseprod_id'=>3,'balanceprod_id'=>1,'identifier'=>'ECAT','naam'=>'EQUILIBRIUM CATALYST','tpto_prodgroup_id'=>2,'purity'=>100,'density'=>0.90,'uncode'=>0,'un_nl'=>'EQUILIBRIUM CATALYST','un_fr'=>'EQUILIBRIUM CATALYST','un_uk'=>'STATISTIC NR. 3815 9090 90','un_de'=>'MANUFACTURING COMPANY: basf2','basepercentage'=>100),
array('isbaseproduct'=>1,'baseprod_id'=>3,'balanceprod_id'=>1,'identifier'=>'USP500','naam'=>'USP 500 PROMOTOR','tpto_prodgroup_id'=>3,'purity'=>100,'density'=>0.90,'uncode'=>0,'un_nl'=>'USP 500 PROMOTOR','un_fr'=>'USP 500 PROMOTOR','un_uk'=>'STATISTIC NR. 3815 9090 90','un_de'=>'MANUFACTURING COMPANY: basf2','basepercentage'=>100),
array('isbaseproduct'=>1,'baseprod_id'=>3,'balanceprod_id'=>1,'identifier'=>'P500','naam'=>'PROCAT 500','tpto_prodgroup_id'=>3,'purity'=>100,'density'=>0.90,'uncode'=>0,'un_nl'=>'PROCAT 500','un_fr'=>'PROCAT 500','un_uk'=>'','un_de'=>'F','basepercentage'=>100),
array('isbaseproduct'=>1,'baseprod_id'=>3,'balanceprod_id'=>1,'identifier'=>'ECATFINES','naam'=>'ECAT FINES','tpto_prodgroup_id'=>2,'purity'=>100,'density'=>0.90,'uncode'=>0,'un_nl'=>'ECAT FINES','un_fr'=>'ECAT FINES','un_uk'=>'','un_de'=>'F','basepercentage'=>100),
array('isbaseproduct'=>1,'baseprod_id'=>3,'balanceprod_id'=>1,'identifier'=>'USP500','naam'=>'USP 500 PROMOTOR','tpto_prodgroup_id'=>3,'purity'=>100,'density'=>0.90,'uncode'=>0,'un_nl'=>'USP 500 PROMOTOR','un_fr'=>'STATISTIC NR: 3815 9090 90','un_uk'=>'','un_de'=>'MANUFACTURING COMPANY: basf2','basepercentage'=>100),
array('isbaseproduct'=>1,'baseprod_id'=>3,'balanceprod_id'=>1,'identifier'=>'FLEXTEC2BP26','naam'=>'Flex-Tec-2BP26','tpto_prodgroup_id'=>1,'purity'=>100,'density'=>0.90,'uncode'=>0,'un_nl'=>'FCC CATALYST, FLEX-TEC-2BP26','un_fr'=>'FCC CATALYST, FLEX-TEC-2BP26','un_uk'=>'STATISTIC NR. 3815 9090 90','un_de'=>'MANUFACTURING COMPANY: basf2','basepercentage'=>100),
array('isbaseproduct'=>1,'baseprod_id'=>3,'balanceprod_id'=>1,'identifier'=>'FLEXTECSPC2','naam'=>'Flex-Tec-SPC2','tpto_prodgroup_id'=>1,'purity'=>100,'density'=>0.90,'uncode'=>0,'un_nl'=>'FCC CATALYST, FLEX-TEC-SPC2','un_fr'=>'FCC CATALYST, FLEX-TEC-SPC2','un_uk'=>'STATISTIC NR. 3815 9090 90','un_de'=>'MANUFACTURING COMPANY: basf2','basepercentage'=>100),
array('isbaseproduct'=>1,'baseprod_id'=>3,'balanceprod_id'=>1,'identifier'=>'FCC E','naam'=>'Fcc Endurance EI02','tpto_prodgroup_id'=>1,'purity'=>100,'density'=>0.90,'uncode'=>0,'un_nl'=>'FCC CATALYST ENDURANCE EI02','un_fr'=>'FCC CATALYST ENDURANCE EI02','un_uk'=>'STATISTIC NR. 3815 9090 90','un_de'=>'MANUFACTURING COMPANY: basf2','basepercentage'=>100),
array('isbaseproduct'=>1,'baseprod_id'=>3,'balanceprod_id'=>1,'identifier'=>'X-MPS-SP4','naam'=>'X-MPS-SP4','tpto_prodgroup_id'=>1,'purity'=>100,'density'=>0.90,'uncode'=>0,'un_nl'=>'FCC CATALYST, X-MPS-SP4','un_fr'=>'FCC CATALYST, X-MPS-SP4','un_uk'=>'STATISTIC NR. 3815 9090 90','un_de'=>'MANUFACTURING COMPANY: basf2','basepercentage'=>100),
array('isbaseproduct'=>1,'baseprod_id'=>3,'balanceprod_id'=>1,'identifier'=>'X20-C6','naam'=>'FCC CATALYST, ADDITIVE','tpto_prodgroup_id'=>3,'purity'=>100,'density'=>0.90,'uncode'=>0,'un_nl'=>'FCC CATALYST, ADDITIVE','un_fr'=>'FCC CATALYST, ADDITIVE','un_uk'=>'STATISTIC NR. 3815 9090 90','MANUFACTURING COMPANY: basf2','basepercentage'=>100),
array('isbaseproduct'=>1,'baseprod_id'=>3,'balanceprod_id'=>1,'identifier'=>'BPGE5','naam'=>'PETROMAX BPGE5','tpto_prodgroup_id'=>1,'purity'=>100,'density'=>0.90,'uncode'=>0,'un_nl'=>'PETROMAX BPGE5','un_fr'=>'PETROMAX BPGE5','un_uk'=>'STATISTIC NR. 3815 9090 90','un_de'=>'MANUFACTURING COMPANY: basf2','basepercentage'=>100)
);

echo create_initial_create_table($t, $Astruct, 'ON [PRIMARY]');
echo create_initial_insert_records($t, $Ainsert);
}


Function create_initial_tpto_closeout($t)
{
$Astruct=array(
array('col'=>'[id]',		'type'=>'[int]',		'param'=>'IDENTITY(1,1) NOT NULL',	'constr'=>'','def'=>"" ),
array('col'=>'[naam]',		'type'=>'[varchar](32)','param'=>'NOT NULL',		'constr'=>'',		'def'=>" DEFAULT('jaar - maand - prod') " ),
array('col'=>'[jaar]','type'=>'[int]','param'=>'NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[tpto_prod_id]','type'=>'[int]','param'=>'NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[qty1jan]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[qty1feb]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[qty1mar]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[qty1apr]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[qty1mei]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[qty1jun]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[qty1jul]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[qty1aug]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[qty1sep]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[qty1oct]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[qty1nov]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[qty1dec]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[qty31dec]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[in_jan]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[in_feb]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[in_mar]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[in_apr]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[in_mei]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[in_jun]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[in_jul]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[in_aug]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[in_sep]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[in_oct]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[in_nov]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[in_dec]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[out_jan]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[out_feb]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[out_mar]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[out_apr]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[out_mei]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[out_jun]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[out_jul]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[out_aug]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[out_sep]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[out_oct]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[out_nov]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[out_dec]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[inter_jan]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[inter_feb]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[inter_mar]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[inter_apr]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[inter_mei]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[inter_jun]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[inter_jul]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[inter_aug]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[inter_sep]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[inter_oct]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[inter_nov]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[inter_dec]','type'=>'[decimal](9,2)','param'=>'NULL',	'constr'=>'',	'def'=>" DEFAULT(0) " ),
array('col'=>'[comment]',			'type'=>'[text]',	'param'=>'NULL',			'constr'=>'',		'def'=>" DEFAULT('') " ),
array('col'=>'[isactive]',			'type'=>'[bit]',	'param'=>'NULL', 			'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[islocked]',			'type'=>'[bit]',	'param'=>'NULL', 			'constr'=>'',		'def'=>" DEFAULT(0) " )
);




echo create_initial_create_table($t, $Astruct, 'ON [PRIMARY]');
}





Function create_initial_tpto_storage($t)
{
$Astruct=array(
array('col'=>'[id]',					'type'=>'[int]',		'param'=>'IDENTITY(1,1) NOT NULL',	'constr'=>'','def'=>"" ),
array('col'=>'[identifier]',			'type'=>'[varchar](12)','param'=>'UNIQUE NOT NULL',		'constr'=>'',		'def'=>" DEFAULT('TK-9901') " ),
array('col'=>'[tpto_source_site_id]',	'type'=>'[int]',		'param'=>'NOT NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[tpto_prod_id]',	'type'=>'[int]',				'param'=>'NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[max_qty]',				'type'=>'[decimal](9,2)','param'=>'NULL',		'constr'=>'',		'def'=>"" ),
array('col'=>'[actual_qty]',			'type'=>'[decimal](9,2)','param'=>'NULL',		'constr'=>'',		'def'=>"" ),
array('col'=>'[tag]',					'type'=>'[varchar](16)','param'=>'NULL',		'constr'=>'',		'def'=>" DEFAULT('LT_9901') " ),
array('col'=>'[realtime_perc]',			'type'=>'[decimal](9,2)','param'=>'NULL',		'constr'=>'',		'def'=>"" ),
array('col'=>'[realtime_update]',		'type'=>'[datetime]',	'param'=>'NULL',		'constr'=>'',		'def'=>"" ),
array('col'=>'[units]',					'type'=>'[varchar](8)',	'param'=>'NULL',			'constr'=>'',		'def'=>" DEFAULT('T') " ),
array('col'=>'[actual_density]',		'type'=>'[decimal](9,2)','param'=>'NULL',		'constr'=>'',		'def'=>"" ),
array('col'=>'[actual_purity]',			'type'=>'[decimal](9,2)','param'=>'NULL',		'constr'=>'',		'def'=>"" ),
array('col'=>'[defaultsequence]',		'type'=>'[int]',		'param'=>'NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[comment]',				'type'=>'[text]',		'param'=>'NULL',			'constr'=>'',		'def'=>" DEFAULT('') " ),
array('col'=>'[isactive]',				'type'=>'[bit]',		'param'=>'NULL', 			'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[islocked]',				'type'=>'[bit]',		'param'=>'NULL', 			'constr'=>'',		'def'=>" DEFAULT(0) " )
);

$Ainsert=array(
//Test Vliegas
array('identifier'=>'TK0000','tpto_source_site_id'=>1,'tpto_prod_id'=>1,'max_qty'=>650,'actual_qty'=>500,'tag'=>'SS2200','units'=>'Ton','actual_density'=>1.1,'actual_purity'=>100,'defaultsequence'=>4),
);

echo create_initial_create_table($t, $Astruct, 'ON [PRIMARY]');
echo create_initial_insert_records($t, $Ainsert);
}

Function create_initial_tpto_order($t,$cust_or_vend)
{
$Astruct=array(
array('col'=>'[id]',					'type'=>'[int]',		'param'=>'IDENTITY(1,1) NOT NULL',	'constr'=>'','def'=>"" ),
array('col'=>'[tpt_ordernr]',			'type'=>'[varchar](16)','param'=>'UNIQUE NOT NULL',			'constr'=>'',		'def'=>" DEFAULT('TPT-in-090001') " ),
array('col'=>'[cust_ordernr]',			'type'=>'[varchar](16)','param'=>'UNIQUE NOT NULL',			'constr'=>'',		'def'=>" DEFAULT('090001') " ),
array('col'=>'[tpto_'.$cust_or_vend.'_id]','type'=>'[int]',		'param'=>'NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[rdate]',					'type'=>'[datetime]',	'param'=>'NULL',		'constr'=>'',		'def'=>"" ),
array('col'=>'[edate]',					'type'=>'[datetime]',	'param'=>'NULL',		'constr'=>'',		'def'=>"" ),
array('col'=>'[adate]',					'type'=>'[datetime]',	'param'=>'NULL',		'constr'=>'',		'def'=>"" ),
array('col'=>'[bdate]',					'type'=>'[datetime]',	'param'=>'NULL',		'constr'=>'',		'def'=>"" ),
array('col'=>'[tpto_forw_id]',			'type'=>'[int]',		'param'=>'NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[tpto_source_site_id]',	'type'=>'[int]',		'param'=>'NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[tpto_dest_site_id]',		'type'=>'[int]',		'param'=>'NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[tpto_prod_id]',			'type'=>'[int]',		'param'=>'NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[order_qty]',				'type'=>'[decimal](9,2)','param'=>'NULL',		'constr'=>'',		'def'=>"" ),
array('col'=>'[tpto_storage_from_id]',	'type'=>'[int]',		'param'=>'NULL',			'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[tpto_storage_to_id]',	'type'=>'[int]',	'param'=>'NULL',			'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[weight1]',				'type'=>'[decimal](9,2)','param'=>'NULL',		'constr'=>'',		'def'=>"" ),
array('col'=>'[weight2]',				'type'=>'[decimal](9,2)','param'=>'NULL',		'constr'=>'',		'def'=>"" ),
array('col'=>'[netweight]',				'type'=>'[decimal](9,2)','param'=>'NULL',		'constr'=>'',		'def'=>"" ),
array('col'=>'[shipref]',				'type'=>'[varchar](24)',	'param'=>'NULL',		'constr'=>'',		'def'=>" DEFAULT('') " ),
array('col'=>'[indigo_status_id]',		'type'=>'[int]',	'param'=>'NULL',			'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[order_log]',				'type'=>'[varchar](255)',	'param'=>'NULL',	'constr'=>'',		'def'=>" DEFAULT('') " ),
array('col'=>'[docs_array]',			'type'=>'[varchar](255)','param'=>'NULL', 		'constr'=>'',		'def'=>" DEFAULT('(CMR)') " ),
array('col'=>'[analyse_array]',			'type'=>'[varchar](255)','param'=>'NULL', 		'constr'=>'',		'def'=>" DEFAULT('') " ),
array('col'=>'[comment]',				'type'=>'[text]',	'param'=>'NULL',			'constr'=>'',		'def'=>" DEFAULT('') " ),
array('col'=>'[isbooked]',				'type'=>'[bit]',	'param'=>'NULL', 				'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[isactive]',				'type'=>'[bit]',	'param'=>'NULL', 				'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[islocked]',				'type'=>'[bit]',	'param'=>'NULL', 				'constr'=>'',		'def'=>" DEFAULT(0) " )
);

$Ainsert=array(
);

echo create_initial_create_table($t, $Astruct, 'ON [PRIMARY]');
echo create_initial_insert_records($t, $Ainsert);

$aConfig=array(
array('sleutel'=>$t.'.id.privs','waarde'=>' (1:r)'),
array('sleutel'=>$t.'.tpt_ordernr.privs','waarde'=>' (1:r)(2:r)(3:r)(11:r)(12:r) '),
array('sleutel'=>$t.'.cust_ordernr.privs','waarde'=>' (1:r)(2:r)(3:r)(11:r)(12:r)(1-1:e)(11-1:e)(12-1:e)) '),
array('sleutel'=>$t.'.tpto_cust_id.privs','waarde'=>' (1:r)(2:r)(3:r)(11:r)(12:r)(1-1:e)(11-1:e)(12-1:e) '),
array('sleutel'=>$t.'.rdate.privs','waarde'=>' (1:r)(2:r)(3:r)(11:r)(12:r) '),
array('sleutel'=>$t.'.edate.privs','waarde'=>' (1:r)(2:r)(3:r)(11:r)(12:r)(1-1:e)(11-1:e)(12-1:e) '),
array('sleutel'=>$t.'.adate.privs','waarde'=>' (1:r)(2:r)(3:r)(11:r)(12:r) '),
array('sleutel'=>$t.'.bdate.privs','waarde'=>' (1:r)(2:r)(3:r)(11:r)(1-2:e)(2-2:e) '),
array('sleutel'=>$t.'.tpto_forw_id.privs','waarde'=>' (1:r)(2:r)(3:r)(11:r)(12:r)(1-1:e)(11-1:e)(12-1:e)'),
array('sleutel'=>$t.'.tpto_source_site_id.privs','waarde'=>' (1:r)(2:r)(3:r)(11:r)(12:r)(1-1:e)(11-1:e)(12-1:e) '),
array('sleutel'=>$t.'.tpto_dest_site_id.privs','waarde'=>' (1:r)(2:r)(3:r)(11:r)(12:r)(1-1:e)(11-1:e)(12-1:e)) '),
array('sleutel'=>$t.'.tpto_prod_id.privs','waarde'=>' (1:r)(2:r)(3:r)(11:r)(12:r)(1-1:e)(11-1:e)(12-1:e) '),
array('sleutel'=>$t.'.order_qty.privs','waarde'=>' (1:r)(2:r)(3:r)(11:r)(12:r)(1-1:e)(11-1:e)(12-1:e)(1-2:e)(2-2:e)(3-2:e) '),
array('sleutel'=>$t.'.tpto_storage_from_id.privs','waarde'=>' (1:r)(2:r)(3:r)(11:r)(12:r)(1-2:e)(2-2:e)(3-2:e) '),
array('sleutel'=>$t.'.tpto_storage_to_id.privs','waarde'=>' (1:r)(2:r)(3:r)(11:r)(12:r)(1-2:e)(2-2:e)(3-2:e)'),
array('sleutel'=>$t.'.shipref.privs','waarde'=>' (1:r)(2:r)(3:r)(11:r)(12:r)(1-2:e)(2-2:e)(3-2:e) '),
array('sleutel'=>$t.'.weight1.privs','waarde'=>' (1:r)(2:r)(3:r)(11:r)(12:r)(1-2:e)(2-2:e)(3-2:e)  '),
array('sleutel'=>$t.'.weight2.privs','waarde'=>' (1:r)(2:r)(3:r)(11:r)(12:r)(1-2:e)(2-2:e)(3-2:e) '),
array('sleutel'=>$t.'.netweight.privs','waarde'=>' (1:r)(2:r)(3:r)(11:r)(12:r) '),
array('sleutel'=>$t.'.indigo_status_id.privs','waarde'=>' (1:e)(2:e)(3:r)(11:r)(12:r) '),
array('sleutel'=>$t.'.order_log.privs','waarde'=>' (1:r)(2:r)(3:r)(11:r)(12:r) '),
array('sleutel'=>$t.'.comment.privs','waarde'=>' (1:r)(2:r)(3:r)(11:r)(12:r)(1-1:e)(11-1:e)(12-1:e)(12-2:e)(2-2:e)(3-2:e) '),
array('sleutel'=>$t.'.isbooked.privs','waarde'=>' (1:r)(11-3:e)(12-3:e) '),
array('sleutel'=>$t.'.isactive.privs','waarde'=>' (1:e)(2:r)(3:r)(11:r)(12:r) '),
array('sleutel'=>$t.'.islocked.privs','waarde'=>' (1:e)(2:r)(3:r)(11:r)(12:r) ')
);


}




Function create_initial_tpto_order_template($t)
{
$Astruct=array(
array('col'=>'[id]',					'type'=>'[int]',		'param'=>'IDENTITY(1,1) NOT NULL',	'constr'=>'',		'def'=>"" ),
array('col'=>'[tpto_cust_id]',			'type'=>'[int]',		'param'=>'NOT NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[tpto_forw_id]',			'type'=>'[int]',		'param'=>'NOT NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[tpto_source_site_id]',	'type'=>'[int]',		'param'=>'NOT NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[tpto_dest_site_id]',		'type'=>'[int]',		'param'=>'NOT NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[tpto_prod_id]',			'type'=>'[int]',		'param'=>'NOT NULL',		'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[order_qty]',				'type'=>'[decimal](9,2)','param'=>'NOT NULL',		'constr'=>'',		'def'=>"" ),
array('col'=>'[last_net_qty]',			'type'=>'[decimal](9,2)','param'=>'NOT NULL',		'constr'=>'',		'def'=>"" ),
array('col'=>'[last_adate]',			'type'=>'[datetime]',	'param'=>'NOT NULL',		'constr'=>'',		'def'=>"" ),
array('col'=>'[tpto_storage_from_id]',	'type'=>'[int]',	'param'=>'NOT NULL',			'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[tpto_storage_to_id]',	'type'=>'[int]',	'param'=>'NOT NULL',			'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[docs_array]',			'type'=>'[varchar](255)',	'param'=>'NULL', 		'constr'=>'',		'def'=>" DEFAULT('(CMR)') " ),
array('col'=>'[analyse_array]',			'type'=>'[varchar](255)',	'param'=>'NULL', 		'constr'=>'',		'def'=>" DEFAULT('') " ),
array('col'=>'[comment]',				'type'=>'[text]',		'param'=>'NULL',			'constr'=>'',		'def'=>" DEFAULT('') " ),
array('col'=>'[isactive]',				'type'=>'[bit]',	'param'=>'NULL', 				'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[islocked]',				'type'=>'[bit]',	'param'=>'NULL', 				'constr'=>'',		'def'=>" DEFAULT(0) " )
);

$Ainsert=array(
);

echo create_initial_create_table($t, $Astruct, 'ON [PRIMARY]');
echo create_initial_insert_records($t, $Ainsert);
}


Function create_initial_tpto_cust($t)
{
$Astruct=array(
array('col'=>'[id]',		'type'=>'[int]',		'param'=>'IDENTITY(1,1) NOT NULL',	'constr'=>'',		'def'=>"" ),
array('col'=>'[identifier]','type'=>'[varchar](12)','param'=>'UNIQUE NOT NULL',			'constr'=>'',		'def'=>" DEFAULT('Klant_identifier') " ),
array('col'=>'[naam]',		'type'=>'[varchar](48)','param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('Klant_naam') " ),
array('col'=>'[address]',	'type'=>'[varchar](48)','param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('Klant_adres') " ),
array('col'=>'[city]',		'type'=>'[varchar](48)','param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('Klant_stad') " ),
array('col'=>'[postalcode]','type'=>'[varchar](16)','param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('Klant_postcode') " ),
array('col'=>'[tpto_country_id]','type'=>'[int]',	'param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[email]',		'type'=>'[varchar](64)','param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('cust@cust.eu') " ),
array('col'=>'[comment]',	'type'=>'[text]',		'param'=>'NULL',					'constr'=>'',		'def'=>" DEFAULT ('') " ),
array('col'=>'[indigo_user_id]','type'=>'[int]',	'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(2) " ),
array('col'=>'[default_qty]',			'type'=>'[decimal](9,2)','param'=>'NULL',		'constr'=>'',		'def'=>" DEFAULT(27) " ),
array('col'=>'[tpto_forw_id]',			'type'=>'[int]',	'param'=>'NULL',		'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[tpto_prod_id]',			'type'=>'[int]',	'param'=>'NULL',		'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[tpto_source_site_id]',	'type'=>'[int]',	'param'=>'NULL',		'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[docs_array]',			'type'=>'[varchar](255)',	'param'=>'NULL', 		'constr'=>'',		'def'=>" DEFAULT('(CMR)') " ),
array('col'=>'[analyse_array]',			'type'=>'[varchar](255)',	'param'=>'NULL', 		'constr'=>'',		'def'=>" DEFAULT('') " ),
array('col'=>'[isactive]',		'type'=>'[bit]',	'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[islocked]',		'type'=>'[bit]',	'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(0) " )
);

if($t=='tpto_cust')
{
$Ainsert=array(
array('identifier'=>'ESSJ', 'naam' =>'ESSO RAFFINAGE SAS - RAF DE PORT JEROME ','address'=> '1 AVENUE KENNEDY' ,'city'=>'NOTRE DAME DE GRAVENCHON', 'postalcode'=>'76330','tpto_country_id'=>4,'indigo_user_id'=>1,'tpto_prod_id'=>1,'tpto_forw_id'=>1,'default_qty'=>30	),
array('identifier'=>'ESUK', 'naam' =>'ESSO PETROLEUM COMPANY LTD','address'=> 'GATE NO. 1 - FAWLEY REFINERY' ,'city'=>'SOUTHAMPTON S4045 1DS', 'postalcode'=>'SURRREY KT22 8UX','tpto_country_id'=>5,'indigo_user_id'=>1,'tpto_prod_id'=>1,'tpto_forw_id'=>1,'default_qty'=>30	),
array('identifier'=>'EAUG', 'naam' =>'ESSO RAFFINERIA DI AUGUSTA','address'=> '' ,'city'=>'SICILY', 'postalcode'=>'96011','tpto_country_id'=>5,'indigo_user_id'=>1,'tpto_prod_id'=>1,'tpto_forw_id'=>1,'default_qty'=>30	),
array('identifier'=>'KREM', 'naam' =>'JSC UKRTATNAFTA','address'=> 'SWISHTOVSKAYA 3' ,'city'=>'KREMENCHUG', 'postalcode'=>'39609','tpto_country_id'=>6,'indigo_user_id'=>1,'tpto_prod_id'=>1,'tpto_forw_id'=>1,'default_qty'=>30	),
array('identifier'=>'ESSA', 'naam' =>'ESSO BELGIUM, DIV.OF EXXON MOBIL PETR & CHEM, BVBA','address'=> 'HAVEN 447, POLDERDIJKWEG 3' ,'city'=>'ANTWERPEN', 'postalcode'=>'2030','tpto_country_id'=>3,'indigo_user_id'=>1,'tpto_prod_id'=>1,'tpto_forw_id'=>1,'default_qty'=>30	),
array('identifier'=>'RUHR', 'naam' =>'RUHR OEL GMBH FCC UNIT','address'=> 'JOHANNASTRASSE 2-8' ,'city'=>'GELSENKIRCHEN-HASSEL', 'postalcode'=>'45899','tpto_country_id'=>2,'indigo_user_id'=>1,'tpto_prod_id'=>1,'tpto_forw_id'=>1,'default_qty'=>30	),
array('identifier'=>'MOSC', 'naam' =>'MOSCOW OIL REFINERY, FCC UNIT','address'=> 'KAPOTNIA, 2 KVARTAL' ,'city'=>'MOSCOW', 'postalcode'=>'109429','tpto_country_id'=>7,'indigo_user_id'=>1,'tpto_prod_id'=>1,'tpto_forw_id'=>1,'default_qty'=>30	),
array('identifier'=>'EING', 'naam' =>'PETROPLUS RAFFINERIE, INGOLSTADT GMBH','address'=> 'ESSOSTRASSE 1' ,'city'=>'KOESCHING', 'postalcode'=>'85092','tpto_country_id'=>2,'indigo_user_id'=>1,'tpto_prod_id'=>1,'tpto_forw_id'=>1,'default_qty'=>30	),
array('identifier'=>'PETRO', 'naam' =>'JSC PETROTEL-LUKOIL, PLOESTI','address'=> 'MIBAU BRAVU STR. 236' ,'city'=>'PRAHOVA COUNTRY', 'postalcode'=>'','tpto_country_id'=>8,'indigo_user_id'=>1,'tpto_prod_id'=>1,'tpto_forw_id'=>1,'default_qty'=>30	),
array('identifier'=>'BPRO', 'naam' =>'BP RAFFINADERIJ ROTTERDAM BV','address'=> 'D`ARCYWEG 76 HAVENNR 6425' ,'city'=>'ROTTERDAM', 'postalcode'=>'3198NA','tpto_country_id'=>1,'indigo_user_id'=>1,'tpto_prod_id'=>1,'tpto_forw_id'=>1,'default_qty'=>30	),
array('identifier'=>'MOH', 'naam' =>'MOTOR OIL (HELLAS) CORINTH','address'=> 'REFINERIES SA , AG, THEODORI' ,'city'=>'CORINTH', 'postalcode'=>'20100','tpto_country_id'=>9,'indigo_user_id'=>1,'tpto_prod_id'=>1,'tpto_forw_id'=>1,'default_qty'=>30	),
array('identifier'=>'SHPC', 'naam' =>'STE COURONNAISE DE RAFFINAGE, RAFFINERIE PETROPLUS','address'=> 'PR. CHI/LAB B&S-PTCO 17' ,'city'=>'PETIT COURONNE', 'postalcode'=>'76650','tpto_country_id'=>4,'indigo_user_id'=>1,'tpto_prod_id'=>1,'tpto_forw_id'=>1,'default_qty'=>30	),
array('identifier'=>'SHPE', 'naam' =>'SHELL PERNISS','address'=> 'STRAATNAAM' ,'city'=>'HOOGVLIET', 'postalcode'=>'1234AA','tpto_country_id'=>1,'indigo_user_id'=>1,'tpto_prod_id'=>1,'tpto_forw_id'=>1,'default_qty'=>30	)

);
}

if($t=='tpto_vend')
{
$Ainsert=array(
//array('identifier'=>'111', 'naam' => 'CLEARWAY SUPPLIER','address'=> '' ,'city'=>'', 'postalcode'=>'111','tpto_country_id'=>1,'indigo_user_id'=>1,'default_qty'=>100	),
);

}
echo create_initial_create_table($t, $Astruct, 'ON [PRIMARY]');
echo create_initial_insert_records($t, $Ainsert);
}

Function create_initial_tpto_forw($t)
{
$Astruct=array(
array('col'=>'[id]',		'type'=>'[int]',		'param'=>'IDENTITY(1,1) NOT NULL',	'constr'=>'',		'def'=>"" ),
array('col'=>'[identifier]','type'=>'[varchar](12)','param'=>'UNIQUE NOT NULL',				'constr'=>'',	'def'=>" DEFAULT('Klant_identifier') " ),
array('col'=>'[naam]',		'type'=>'[varchar](48)','param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('Klant_naam') " ),
array('col'=>'[address]',	'type'=>'[varchar](48)','param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('Klant_adres') " ),
array('col'=>'[city]',		'type'=>'[varchar](48)','param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('Klant_stad') " ),
array('col'=>'[postalcode]','type'=>'[varchar](16)','param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('Klant_postcode') " ),
array('col'=>'[tpto_country_id]','type'=>'[int]',	'param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[email]',		'type'=>'[varchar](64)','param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('cust@cust.eu') " ),
array('col'=>'[comment]',	'type'=>'[text]',		'param'=>'NULL',					'constr'=>'',		'def'=>" DEFAULT ('') " ),
array('col'=>'[indigo_user_id]','type'=>'[int]',	'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(2) " ),
array('col'=>'[isactive]',		'type'=>'[bit]',	'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[islocked]',		'type'=>'[bit]',	'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(0) " )
);

$Ainsert=array(
array('identifier'=>'NILL', 'naam' => 'INT. TRANSPORTBEDRIJF NILLEZEN BV','address'=> 'STATIONSWEG 1' ,'city'=>'OEFFELT', 'postalcode'=>'5441AG','tpto_country_id'=>1,'indigo_user_id'=>1 ),
array('identifier'=>'HUKT', 'naam' => 'HUKTRA TRANSPORTZONE','address'=> 'KOGGENSTRAAT 12' ,'city'=>'ZEEBRUGGE', 'postalcode'=>'8380','tpto_country_id'=>3,'indigo_user_id'=>1	),
array('identifier'=>'BERT', 'naam' => 'BERTSCHI','address'=> 'LEBZZBURERSTR. 2' ,'city'=>'BIRR', 'postalcode'=>'5242','tpto_country_id'=>9,'indigo_user_id'=>1	),
array('identifier'=>'WESEX', 'naam' => 'WEST EXPRESS','address'=> 'MOSKOVSKAYA ST 37' ,'city'=>'KREMENCHUG', 'postalcode'=>'039626','tpto_country_id'=>6,'indigo_user_id'=>1	),
array('identifier'=>'RIJKE', 'naam' => 'DE RIJKE LOGISTIC','address'=> 'STRAAT' ,'city'=>'PLAATS', 'postalcode'=>'1234','tpto_country_id'=>1,'indigo_user_id'=>1	)
);

echo create_initial_create_table($t, $Astruct, 'ON [PRIMARY]');
echo create_initial_insert_records($t, $Ainsert);
}


Function create_initial_tpto_site($t)
{
$Astruct=array(
array('col'=>'[id]',		'type'=>'[int]',		'param'=>'IDENTITY(1,1) NOT NULL',	'constr'=>'',		'def'=>"" ),
array('col'=>'[identifier]','type'=>'[varchar](12)','param'=>'UNIQUE NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('Klant_identifier') " ),
array('col'=>'[naam]',		'type'=>'[varchar](64)','param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('Klant_naam') " ),
array('col'=>'[address]',	'type'=>'[varchar](64)','param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('Klant_adres') " ),
array('col'=>'[city]',		'type'=>'[varchar](64)','param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('Klant_stad') " ),
array('col'=>'[postalcode]','type'=>'[varchar](16)','param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('Klant_postcode') " ),
array('col'=>'[tpto_country_id]','type'=>'[int]',	'param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[email]',		'type'=>'[varchar](64)','param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('cust@cust.eu') " ),
array('col'=>'[comment]',	'type'=>'[text]',		'param'=>'NULL',					'constr'=>'',		'def'=>" DEFAULT ('') " ),
array('col'=>'[indigo_user_id]','type'=>'[int]',	'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(2) " ),
array('col'=>'[isactive]',		'type'=>'[bit]',	'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[islocked]',		'type'=>'[bit]',	'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(0) " )
);

$Ainsert=array(
array('identifier'=>'TPT', 'naam' => 'TPT BV','address'=> 'Finlandweg 21' ,'city'=>'Terneuzen', 'postalcode'=>'4538BL','tpto_country_id'=>1,'indigo_user_id'=>1	),
);

echo create_initial_create_table($t, $Astruct, 'ON [PRIMARY]');
echo create_initial_insert_records($t, $Ainsert);
}





Function create_initial_tpto_country($t)
{
$Astruct=array(
array('col'=>'[id]',			'type'=>'[int]',			'param'=>'IDENTITY(1,1) NOT NULL',	'constr'=>'',		'def'=>"" ),
array('col'=>'[naam]',			'type'=>'[varchar](16)',	'param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('Land') " ),
array('col'=>'[identifier]',	'type'=>'[varchar](4)',		'param'=>'UNIQUE NOT NULL',				'constr'=>'',		'def'=>"" ),
array('col'=>'[maxt]',			'type'=>'[decimal](6,2)',	'param'=>'NOT NULL',				'constr'=>'',		'def'=>"" ),
array('col'=>'[isactive]',		'type'=>'[bit]',			'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[islocked]',		'type'=>'[bit]',			'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(0) " )
);

$Ainsert=array(
array('naam'=>'Nederland',	'identifier'=>'nl','maxt'=>50),
array('naam'=>'Duitsland',	'identifier'=>'du','maxt'=>40),
array('naam'=>'Belgie',		'identifier'=>'be','maxt'=>44),
array('naam'=>'Frankrijk',	'identifier'=>'fr','maxt'=>40),
array('naam'=>'UNITED KINGDOM',	'identifier'=>'uk','maxt'=>40),
array('naam'=>'UKRAINE',	'identifier'=>'ue','maxt'=>40),
array('naam'=>'RUSSIA',		'identifier'=>'ru','maxt'=>40),
array('naam'=>'ROMANIA',	'identifier'=>'rm','maxt'=>40),
array('naam'=>'GREECE',		'identifier'=>'gr','maxt'=>40),
array('naam'=>'SWISS',		'identifier'=>'sw','maxt'=>40)
);

echo create_initial_create_table($t, $Astruct, 'ON [PRIMARY]');
echo create_initial_insert_records($t, $Ainsert);
}




// *************************************************
// ***** START FUNCTIONS CREATE INITIAL TABLES *****
// *************************************************
Function create_initial_indigo_tags($t)
{
$Astruct=array(
array('col'=>'[id]',			'type'=>'[int]',			'param'=>'IDENTITY(1,1) NOT NULL',	'constr'=>'',		'def'=>"" ),
array('col'=>'[naam]',			'type'=>'[varchar](32)',	'param'=>'UNIQUE NOT NULL',			'constr'=>'',		'def'=>" DEFAULT('tablename') " ),
array('col'=>'[idcolumn]',		'type'=>'[varchar](16)',	'param'=>'NULL',				'constr'=>'',		'def'=>" DEFAULT('id') " ),
array('col'=>'[maincolumn]',	'type'=>'[varchar](32)',	'param'=>'NULL',				'constr'=>'',		'def'=>" DEFAULT('naam') " ),
array('col'=>'[lingual]',		'type'=>'[text]',			'param'=>'NULL',					'constr'=>'',		'def'=>" DEFAULT('title:en(title) title:nl(titel) title:fr(title)decription:en(decription) title:nl(beschrijving) title:fr(details)') " ),
array('col'=>'[indigo_user_id]','type'=>'[int]',			'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(2) " ),
array('col'=>'[parent_id]',		'type'=>'[int]',			'param'=>'NULL', 					'constr'=>'',		'def'=>"" ),
array('col'=>'[icon_img]',		'type'=>'[varchar](80)',	'param'=>'NULL', 				'constr'=>'',		'def'=>"" ),
array('col'=>'[sequence]',		'type'=>'[int]',			'param'=>'NULL', 					'constr'=>'',		'def'=>"" ),
array('col'=>'[browse_file]',	'type'=>'[varchar](80)',	'param'=>'NULL', 				'constr'=>'',		'def'=>" DEFAULT('webpage.php') " ),
array('col'=>'[css_file]',		'type'=>'[varchar](80)',	'param'=>'NULL', 				'constr'=>'',		'def'=>" DEFAULT('lib_indigo.css') " ),
array('col'=>'[browseclass]',	'type'=>'[varchar](80)',	'param'=>'NULL', 				'constr'=>'',		'def'=>" DEFAULT('veld_naam_middel') " ),
array('col'=>'[ncolumns]',		'type'=>'[int]',			'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(-1)" ),
array('col'=>'[rowsperpage]',	'type'=>'[int]',			'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(200)" ),
array('col'=>'[recorddetail_Xsize]','type'=>'[int]',		'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(666)" ),
array('col'=>'[recorddetail_Ysize]','type'=>'[int]',		'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(800)" ),
array('col'=>'[orderby]',		'type'=>'[varchar](80)',	'param'=>'NULL', 					'constr'=>'',		'def'=>"" ),
array('col'=>'[visiblecolumns]','type'=>'[text]',	'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT('naam:334')" ),
array('col'=>'[access]',		'type'=>'[varchar](255)',	'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT('(1:e)(2:e)(3:r)(11:r)(12:r)') " ),
array('col'=>'[isactive]',		'type'=>'[bit]',			'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[islocked]',		'type'=>'[bit]',			'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[istable]',		'type'=>'[bit]',			'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[isintoolbar]',	'type'=>'[bit]',			'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(0) " )
);

$Ainsert=array(array('naam'=>'home','parent_id'=>0,'sequence'=>1,'icon_img'=>'home','lingual'=>'title:all(home)description:all(homepage)description:nl(startpagina)','isintoolbar'=>1),
array('naam'=>'systemtables',		'parent_id'=>1,'sequence'=>3,'icon_img'=>'table','lingual'=>'title:all(systables)description:nl(systeem tabellen)description:all(system tables)'),
array('naam'=>'help',				'parent_id'=>1,'sequence'=>4,'icon_img'=>'help','lingual'=>'title:all(help page) title:nl(help pagina) description:all(info)'),
array('naam'=>'developer',			'parent_id'=>1,'sequence'=>5,'icon_img'=>'programmer','lingual'=>'title:en(developer) title:nl(ontwikkelaar) title:fr(programmeur) description:all(private area)'),
array('naam'=>'storage',			'parent_id'=>1,'sequence'=>21,'icon_img'=>'organize','lingual'=>'title:all(Storage)title:nl(Voorraad)description:all(Inventory Control)description:nl(beheer & orders & planning)','isintoolbar'=>1),

array('naam'=>'developer/aLiB_tagvar',		'parent_id'=>4,'sequence'=>6,'icon_img'=>'find','lingual'=>'title:all(aLiB_tag)description:all(Array variable holding tag)'),
array('naam'=>'developer/sessionvar',		'parent_id'=>4,'sequence'=>7,'icon_img'=>'find2','lingual'=>'title:all(_SESSION)description:all(Session array holding public vars)'),
array('naam'=>'developer/icons',			'parent_id'=>4,'sequence'=>9,'icon_img'=>'small-icons','lingual'=>'title:all(icons) description:all(available in this enviroment)'),
array('naam'=>'developer/tables',			'parent_id'=>4,'sequence'=>8,'icon_img'=>'db','lingual'=>'title:all(DB)description:all(Tables & Views)'),
array('naam'=>'developer/sandbox',			'parent_id'=>4,'sequence'=>10,'icon_img'=>'wizard','lingual'=>'title:all(sandbox) description:all(trial & error)'),
array('naam'=>'developer/convert_db',		'parent_id'=>4,'sequence'=>11,'icon_img'=>'convert','lingual'=>'title:all(records) description:all(import table)'),

array('naam'=>'tpto_cust',	'parent_id'=>5,'sequence'=>31,'istable'=>1,'browse_file'=>'tag.php','orderby'=>'identifier asc','icon_img'=>'femalecust','lingual'=>'title:all(accounts)title:nl(klanten),description:all(clients & destinatitions)description:nl(bestemmingen)','isintoolbar'=>0,
'visiblecolumns'=>'(identifier:100),(naam:180),(postalcode:150),(city:150),(default_qty:100),(tpto_prod_id:100),(tpto_country_id:100)','isintoolbar'=>1,'maincolumn'=>'identifier'),

array('naam'=>'tpto_vend',	'parent_id'=>5,'sequence'=>32,'istable'=>1,'browse_file'=>'tag.php','orderby'=>'identifier asc','icon_img'=>'malecust','lingual'=>'title:all(suppliers),description:all(sources)','isintoolbar'=>0,
'visiblecolumns'=>'(identifier:100),(naam:180),(postalcode:150),(city:150),(tpto_country_id:100)','isintoolbar'=>1,'maincolumn'=>'identifier'),

array('naam'=>'tpto_forw',	'parent_id'=>5,'sequence'=>33,'istable'=>1,'browse_file'=>'tag.php','orderby'=>'identifier asc','icon_img'=>'truck','lingual'=>'title:all(forwarders)title:nl(vervoerders),description:all(transport companies)description:nl(transport bedrijven)','isintoolbar'=>0,
'visiblecolumns'=>'(identifier:100),(naam:180),(postalcode:150),(city:150),(tpto_country_id:100)','isintoolbar'=>1,'maincolumn'=>'identifier'),

array('naam'=>'tpto_site',	'parent_id'=>5,'sequence'=>34,'istable'=>1,'browse_file'=>'tag.php','orderby'=>'naam asc','icon_img'=>'site','lingual'=>'title:all(sites)title:nl(vestigingen),description:all(locations, departements etc.)description:nl(locaties, afdelingen, etc)','isintoolbar'=>0,'visiblecolumns'=>'(identifier:100),(naam:100),(city:100),(tpto_country_id:100)','isintoolbar'=>0),
array('naam'=>'tpto_country',	'parent_id'=>5,'sequence'=>35,'istable'=>1,'browse_file'=>'tag.php','orderby'=>'naam asc','icon_img'=>'globe','lingual'=>'title:all(countries)title:nl(landen),description:all(source, destinations)description:nl(locaties, afdelingen, etc)','isintoolbar'=>0),
array('naam'=>'tpto_closeout',	'parent_id'=>5,'sequence'=>39,'istable'=>1,'browse_file'=>'tag.php','orderby'=>'id asc','icon_img'=>'add-database_48x','lingual'=>'title:all(closeout),description:all(monthly figures))','isintoolbar'=>1),

array('naam'=>'tpto_in','maincolumn'=>'tpt_ordernr','parent_id'=>5,'sequence'=>11,'istable'=>1,'browse_file'=>'tagorder.php','orderby'=>'tpt_ordernr asc','icon_img'=>'incoming','lingual'=>'title:all(incoming)title:nl(inkomend),description:all(receiving orders)description:nl(te ontvangen vrachten)',
'visiblecolumns'=>	'(tpt_ordernr:tpt_nr:120),(cust_ordernr:cust_nr:80),(rdate:80),(edate:100),(adate:80),(bdate:100),(tpto_forw_id:forw:80),(tpto_prod_id:prod:80),(order_qty:qty:60),(shipref:ref:100),(weight1:60),(weight2:60),(netweight:nett:80)','isintoolbar'=>1),
array('naam'=>'tpto_inter','maincolumn'=>'tpt_ordernr','parent_id'=>5,'sequence'=>12,'istable'=>1,'browse_file'=>'tagorder.php','orderby'=>'tpt_ordernr asc','icon_img'=>'intersilo','lingual'=>'title:all(intersilo)title:nl(intersilo),description:all(internal orders)description:nl(interne voorraadbewegingen)',
'visiblecolumns'=>'(tpt_ordernr:tpt_nr:120),(cust_ordernr:cust_nr:80),(rdate:80),(edate:100),(adate:80),(bdate:100),(tpto_forw_id:forw:80),(tpto_prod_id:prod:80),(order_qty:qty:60),(shipref:ref:100),(weight1:60),(weight2:60),(netweight:nett:80)','isintoolbar'=>0),
array('naam'=>'tpto_out','maincolumn'=>'tpt_ordernr','parent_id'=>5,'sequence'=>12,'istable'=>1,'browse_file'=>'tagorder.php','orderby'=>'tpt_ordernr asc','icon_img'=>'outgoing','lingual'=>'title:all(outgoing)title:nl(uitgaand),description:all(outgoing orders)description:nl(uitgaande vrachten)',
'visiblecolumns'=>'(tpt_ordernr:tpt_nr:120),(cust_ordernr:cust_nr:80),(tpto_cust_id:cust:95),(rdate:80),(edate:100),(adate:80),(bdate:100),(tpto_forw_id:forw:80),(tpto_prod_id:prod:80),(order_qty:qty:60),(shipref:ref:100),(weight1:60),(weight2:60),(netweight:nett:80)','isintoolbar'=>1),

array('naam'=>'tpto_prodgroup','parent_id'=>5,'sequence'=>22,'istable'=>1,'browse_file'=>'tag.php','orderby'=>'identifier asc','icon_img'=>'productgroup','lingual'=>'title:all(prodgroup)title:nl(productgroep),description:all(product groups)description:nl(product groepen)',
'visiblecolumns'=>'(identifier:100),(naam:180),(comment:150)','isintoolbar'=>1,'maincolumn'=>'identifier'),
array('naam'=>'tpto_prod','parent_id'=>5,'sequence'=>23,'istable'=>1,'browse_file'=>'tag.php','orderby'=>'identifier asc','icon_img'=>'product','lingual'=>'title:all(product)title:nl(producten),description:all(product-table)description:nl(producten tabel)',
'visiblecolumns'=>'(identifier:100),(naam:180),(purity:80),(density:80),(uncode:80),(isbaseproduct:isbase:50),
(baseprod_id:base:80),(basepercentage:base%:50),
(mix1prod_id:mix1:80),(mix1percentage:mix1%:50),
(mix2prod_id:mix2:80),(mix2percentage:mix2%:50),
(mix3prod_id:mix3:80),(mix3percentage:mix3%:50),
(balanceprod_id:balance:80),(balancepercentage:mix1%:50),
(tpto_prodgroup_id:prodgroup:150)','isintoolbar'=>1,'maincolumn'=>'identifier'),
array('naam'=>'tpto_storage','parent_id'=>5,'sequence'=>40,'istable'=>1,'browse_file'=>'tag.php','orderby'=>'id asc','icon_img'=>'woodenbucket','lingual'=>'title:all(storage)title:nl(opslag),description:all(silos & bins)description:nl(silos en tanks)','isintoolbar'=>0),
array('naam'=>'tpto_order_template','parent_id'=>5,'sequence'=>41,'istable'=>1,'browse_file'=>'tag.php','maincolumn'=>'id','orderby'=>'id asc','icon_img'=>'template','lingual'=>'title:all(template)title:nl(template),description:all(order-entry defaults)description:nl(defaults voor orderinvoer)','isintoolbar'=>0),

array('naam'=>'tpto_in_history','maincolumn'=>'tpt_ordernr','parent_id'=>5,'sequence'=>42,'istable'=>1,'browse_file'=>'tagorder.php','orderby'=>'id asc','icon_img'=>'history_incoming','lingual'=>'title:all(history_incoming)title:nl(archief_inkomend),description:all(received)description:nl(ontvangen)',
'visiblecolumns'=>'(tpt_ordernr:tpt_nr:120),(cust_ordernr:cust_nr:80),(rdate:80),(edate:100),(adate:80),(bdate:100),(tpto_forw_id:forw:80),(tpto_prod_id:prod:80),(order_qty:qty:60),(shipref:ref:100),(weight1:60),(weight2:60),(netweight:nett:80)','isintoolbar'=>0),
array('naam'=>'tpto_inter_history','maincolumn'=>'tpt_ordernr','parent_id'=>5,'sequence'=>43,'istable'=>1,'browse_file'=>'tagorder.php','orderby'=>'id asc','icon_img'=>'history_intersilo','lingual'=>'title:all(history_intersilo)title:nl(archief_intersilo),description:all(internal transported)description:nl(intern verscheept)',
'visiblecolumns'=>'(tpt_ordernr:tpt_nr:120),(cust_ordernr:cust_nr:80),(rdate:80),(edate:100),(adate:80),(bdate:100),(tpto_forw_id:forw:80),(tpto_prod_id:prod:80),(order_qty:qty:60),(shipref:ref:100),(weight1:60),(weight2:60),(netweight:nett:80)','isintoolbar'=>0),
array('naam'=>'tpto_out_history','maincolumn'=>'tpt_ordernr','parent_id'=>5,'sequence'=>44,'istable'=>1,'browse_file'=>'tagorder.php','orderby'=>'id asc','icon_img'=>'history_outgoing','lingual'=>'title:all(history_outgoing)title:nl(archief_uitgaand),description:all(shipped)description:nl(verscheept)',
'visiblecolumns'=>'(tpt_ordernr:tpt_nr:120),(cust_ordernr:cust_nr:80),(tpto_cust_id:cust:100),(rdate:80),(edate:100),(adate:80),(bdate:100),(tpto_forw_id:forw:80),(tpto_prod_id:prod:80),(order_qty:qty:60),(shipref:ref:100),(weight1:60),(weight2:60),(netweight:nett:80)','isintoolbar'=>0),

array('naam'=>'indigo_tags',		'parent_id'=>2,'sequence'=>2,'istable'=>1,'browse_file'=>'tag.php','recorddetail_Ysize'=>1000,'orderby'=>'naam asc','icon_img'=>'tools','lingual'=>'title:all(indigo_tags) description:all(properties)',
'visiblecolumns'=>'(naam:100),(idcolumn:100),(maincolumn:150),(parent_id:100),(isintoolbar:100),(icon_img:100)','isintoolbar'=>1),
array('naam'=>'indigo_accessgroup',	'parent_id'=>2,'sequence'=>12,'istable'=>1,'browse_file'=>'tag.php','recorddetail_Ysize'=>500,'orderby'=>'naam asc','icon_img'=>'usergroup','lingual'=>'title:all(indigo_accessgroup) description:all(user groups/roles) '),
array('naam'=>'indigo_user',		'parent_id'=>2,'sequence'=>13,'istable'=>1,'browse_file'=>'tag.php','recorddetail_Ysize'=>500,'orderby'=>'naam asc','icon_img'=>'piongreen','lingual'=>'title:all(indigo_user) description:all(logins)'),
array('naam'=>'indigo_customer',	'parent_id'=>2,'sequence'=>14,'istable'=>1,'browse_file'=>'tag.php','recorddetail_Ysize'=>500,'orderby'=>'naam asc','icon_img'=>'customer','lingual'=>'title:all(indigo_customer) description:all(system clients)'),
array('naam'=>'indigo_status',		'parent_id'=>2,'sequence'=>15,'istable'=>1,'browse_file'=>'tag.php','recorddetail_Ysize'=>500,'orderby'=>'naam asc','icon_img'=>'start','lingual'=>'title:all(indigo_status) description:all(various statuses)'),
array('naam'=>'indigo_config',		'parent_id'=>2,'sequence'=>16,'istable'=>1,'maincolumn'=>'sleutel','browse_file'=>'tag.php','recorddetail_Ysize'=>500,  'visiblecolumns'=>'(sleutel:150),(waarde:700),(description:250)', 'orderby'=>'sleutel asc','icon_img'=>'config','lingual'=>'title:all(indigo_config) description:all(keys & values)'),
array('naam'=>'indigo_language',	'parent_id'=>2,'sequence'=>17,'istable'=>1,'browse_file'=>'tag.php','recorddetail_Ysize'=>500,'orderby'=>'naam asc','icon_img'=>'web','lingual'=>'title:all(indigo_language) description:all(multi-lingual)'),

);

echo create_initial_create_table($t, $Astruct, 'ON [PRIMARY]');
echo create_initial_insert_records($t, $Ainsert);
}

Function create_initial_indigo_user($t)
{
$Astruct=array(
array('col'=>'[id]',					'type'=>'[int]',			'param'=>'IDENTITY(1,1) NOT NULL',	'constr'=>'',		'def'=>"" ),
array('col'=>'[naam]',					'type'=>'[varchar](16)',	'param'=>'UNIQUE NOT NULL',			'constr'=>'',		'def'=>" DEFAULT('') " ),
array('col'=>'[fullname]',				'type'=>'[varchar](32)',	'param'=>'NULL',					'constr'=>'',		'def'=>"" ),
array('col'=>'[password]',				'type'=>'[varchar](64)',	'param'=>'NOT NULL',				'constr'=>'',		'def'=>"" ),
array('col'=>'[startpage]',				'type'=>'[varchar](64)',	'param'=>'NULL',					'constr'=>'',		'def'=>"" ),
array('col'=>'[email]',					'type'=>'[varchar](64)',	'param'=>'NULL',					'constr'=>'',		'def'=>"" ),
array('col'=>'[regdate]',				'type'=>'[datetime]',		'param'=>'NULL',					'constr'=>'',		'def'=>"" ),
array('col'=>'[accesslevel]',			'type'=>'[int]',			'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(0) " ),
array('col'=>'[indigo_accessgroup_id]',	'type'=>'[int]',			'param'=>'NULL', 					'constr'=>'',		'def'=>"" ),
array('col'=>'[indigo_language_id]',	'type'=>'[int]',			'param'=>'NULL', 					'constr'=>'',		'def'=>"" ),
array('col'=>'[indigo_customer_id]',	'type'=>'[int]',			'param'=>'NULL', 					'constr'=>'',		'def'=>"" ),
array('col'=>'[lastlogin]',				'type'=>'[datetime]',		'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT('01-01-1980 00:00:00') "),
array('col'=>'[details]',				'type'=>'[text]',			'param'=>'NULL',					'constr'=>'',		'def'=>" DEFAULT('') " ),
array('col'=>'[isactive]',				'type'=>'[bit]',			'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[islocked]',				'type'=>'[bit]',			'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(0) ")
);

$Ainsert=array(
//array('naam'=>'lib','fullname'=>'lieuwe bakker','password'=>md5('amigo'),'startpage'=>'welcome.php','email'=>'lbakker@tpt.nl','regdate'=>date('d-m-y H:m:s'), 'accesslevel'=>1, 'indigo_accessgroup_id'=>1,'indigo_customer_id'=>1, 'details'=>'inserted initially'),
//array('naam'=>'mb','fullname'=>'michel bakker','password'=>md5('micbak'),'startpage'=>'welcome.php','email'=>'mbakker@tpt.nl','regdate'=>date('d-m-y H:m:s'), 'accesslevel'=>2, 'indigo_accessgroup_id'=>1,'indigo_customer_id'=>1, 'details'=>'inserted initially'),
//array('naam'=>'ddk','fullname'=>'dennis de kraker','password'=>md5('ddk123'),'startpage'=>'welcome.php','email'=>'ddk@tpt.nl','regdate'=>date('d-m-y H:m:s'), 'accesslevel'=>2, 'indigo_accessgroup_id'=>1,'indigo_customer_id'=>1, 'details'=>'inserted initially'),
array('naam'=>'fd','fullname'=>'fred dijkstra','password'=>md5('freD#2011'),'startpage'=>'welcome.php','email'=>'fdijkstra@tpt.nl','regdate'=>date('d-m-y H:m:s'), 'accesslevel'=>2, 'indigo_accessgroup_id'=>1,'indigo_customer_id'=>1, 'details'=>'inserted initially'),
//array('naam'=>'admin','fullname'=>'Administrator','password'=>md5('MASTERPASS'),'startpage'=>'welcome.php','email'=>'admin@tpt.nl','regdate'=>date('d-m-y H:m:s'), 'accesslevel'=>2, 'indigo_accessgroup_id'=>1,'indigo_customer_id'=>1, 'details'=>'inserted initially'),
//array('naam'=>'basf2','fullname'=>'basf2 user','password'=>md5('basf2'),'startpage'=>'welcome.php','email'=>'basf2@customer.nl','regdate'=>date('d-m-y H:m:s'), 'accesslevel'=>12, 'indigo_accessgroup_id'=>2,'indigo_customer_id'=>2, 'details'=>'inserted initially'),
//array('naam'=>'adminbasf2','fullname'=>'basf2 admin','password'=>md5('adminbasf2'),'startpage'=>'welcome.php','email'=>'admin@customer.nl','regdate'=>date('d-m-y H:m:s'), 'accesslevel'=>11, 'indigo_accessgroup_id'=>2,'indigo_customer_id'=>2, 'details'=>'inserted initially')
);


//echo create_initial_create_table($t, $Astruct, 'ON [PRIMARY]');
echo create_initial_insert_records($t, $Ainsert);
}

Function create_initial_indigo_accessgroup($t)
{
$Astruct=array(
array('col'=>'[id]',			'type'=>'[int]',			'param'=>'IDENTITY(1,1) NOT NULL',	'constr'=>'',		'def'=>"" ),
array('col'=>'[naam]',			'type'=>'[varchar](16)',	'param'=>'UNIQUE NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('Group Name') " ),
array('col'=>'[icon_img]',		'type'=>'[varchar](80)',	'param'=>'NULL', 					'constr'=>'',		'def'=>"" ),
array('col'=>'[userlevel]',		'type'=>'[int]',			'param'=>'NOT NULL', 				'constr'=>'',		'def'=>" DEFAULT (99) " ),
array('col'=>'[description]',	'type'=>'[text]',			'param'=>'NULL',					'constr'=>'',		'def'=>" DEFAULT ('') " ),
array('col'=>'[isactive]',		'type'=>'[bit]',			'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT (1) " ),
array('col'=>'[islocked]',		'type'=>'[bit]',			'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT (1) " )
);

$Ainsert=array(
array('naam'=>'TPT',	'userlevel'=>1,'icon_img'=>'../image/programmer32.png', 'description'=>'inserted initially'),
array('naam'=>'BAS','userlevel'=>2,'icon_img'=>'../image/userred32.png', 'description'=>'inserted initially'),
);

echo create_initial_create_table($t, $Astruct, 'ON [PRIMARY]');
echo create_initial_insert_records($t, $Ainsert);
}

Function create_initial_indigo_language($t)
{
$Astruct=array(
array('col'=>'[id]',			'type'=>'[int]',			'param'=>'IDENTITY(1,1) NOT NULL',	'constr'=>'',		'def'=>"" ),
array('col'=>'[naam]',			'type'=>'[varchar](16)',	'param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('Language') " ),
array('col'=>'[identifier]',	'type'=>'[varchar](4)',		'param'=>'UNIQUE NOT NULL',				'constr'=>'',		'def'=>"" ),
array('col'=>'[isactive]',		'type'=>'[bit]',			'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[islocked]',		'type'=>'[bit]',			'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(1) " )
);

$Ainsert=array(
array('naam'=>'English',	'identifier'=>'en'),
array('naam'=>'Nederlands',	'identifier'=>'nl'),
array('naam'=>'Frans',		'identifier'=>'fr')
);

echo create_initial_create_table($t, $Astruct, 'ON [PRIMARY]');
echo create_initial_insert_records($t, $Ainsert);
}

Function create_initial_indigo_config($t)
{
$Astruct=array(
array('col'=>'[id]',			'type'=>'[int]',			'param'=>'IDENTITY(1,1) NOT NULL',	'constr'=>'',		'def'=>"" ),
array('col'=>'[indigo_config_cat_id]','type'=>'[int]',			'param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[sleutel]',		'type'=>'[varchar](128)',	'param'=>'UNIQUE NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('Config Key')" ),
array('col'=>'[waarde]',		'type'=>'[text]',			'param'=>'NULL',					'constr'=>'',		'def'=>"" ),
array('col'=>'[optie_veld]',		'type'=>'[text]',			'param'=>'NULL',					'constr'=>'',		'def'=>"" ),
array('col'=>'[privs]',		'type'=>'[text]',			'param'=>'NULL',					'constr'=>'',		'def'=>"" ),
array('col'=>'[description]',	'type'=>'[text]',			'param'=>'NULL',					'constr'=>'',		'def'=>"" ),
array('col'=>'[isactive]',		'type'=>'[bit]',			'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[islocked]',		'type'=>'[bit]',			'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(1) " ));

$Ainsert=array(
array('sleutel'=>'admin_record_unlock_key',	'waarde'=>'v'),
array('sleutel'=>'screenwidth_lib',		'waarde'=>'1800'),
array('sleutel'=>'screenwidth_basf2',		'waarde'=>'1800'),
array('sleutel'=>'convert_tablename',
'waarde'=>
'(tablename:=tablename)
(baseprod:=tpto_prod)
(mix1prod:=tpto_prod)
(mix2prod:=tpto_prod)
(mix3prod:=tpto_prod)
(balanceprod:=tpto_prod)
(tpto_source_site:=tpto_site)
(tpto_dest_site:=tpto_site)
(tpto_storage_from:=tpto_storage)
(tpto_storage_to:=tpto_storage)')
);
echo create_initial_create_table($t, $Astruct, 'ON [PRIMARY]');
echo create_initial_insert_records($t, $Ainsert);
}



Function create_initial_indigo_config_cat($t)
{
	$Astruct=array(
	array('col'=>'[id]',		'type'=>'[int]',			'param'=>'IDENTITY(1,1) NOT NULL',	'constr'=>'',	'def'=>"" ),
	array('col'=>'[naam]',		'type'=>'[varchar](32)',	'param'=>'UNIQUE NOT NULL',		'constr'=>'',	'def'=>" DEFAULT('CATEGORIE')" ),
	array('col'=>'[isactive]',	'type'=>'[bit]',			'param'=>'NULL', 				'constr'=>'',	'def'=>" DEFAULT(1) " ),
	array('col'=>'[islocked]',	'type'=>'[bit]',			'param'=>'NULL', 				'constr'=>'',	'def'=>" DEFAULT(0) " ));

	$Ainsert=array(
	array('naam'=>'general'),
	array('naam'=>'cmr'),
	array('naam'=>'indigo_tag'),
	array('naam'=>'privs'),
	array('naam'=>'widget')
);

echo create_initial_create_table($t, $Astruct, 'ON [PRIMARY]');
echo create_initial_insert_records($t, $Ainsert);
}

Function create_initial_indigo_customer($t)
{
$Astruct=array(
array('col'=>'[id]',			'type'=>'[int]',			'param'=>'IDENTITY(1,1) NOT NULL',	'constr'=>'',		'def'=>"" ),
array('col'=>'[naam]',			'type'=>'[varchar](16)',	'param'=>'NOT NULL',				'constr'=>'',		'def'=>'DEFAULT ("Indigo Cust")' ),
array('col'=>'[identifier]',	'type'=>'[varchar](4)',		'param'=>'UNIQUE NOT NULL',				'constr'=>'',		'def'=>"" ),
array('col'=>'[address]',		'type'=>'[varchar](64)',	'param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('Indigo_cust_adres') " ),
array('col'=>'[city]',			'type'=>'[varchar](64)',	'param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('Indigo_cust_stad') " ),
array('col'=>'[postalcode]',	'type'=>'[varchar](16)',	'param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT('Indigo_cust_postcode') " ),
array('col'=>'[tpto_country_id]','type'=>'[int]',			'param'=>'NOT NULL',				'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[indigo_user_id]','type'=>'[int]',			'param'=>'NULL', 					'constr'=>'',		'def'=>'DEFAULT (2)' ),
array('col'=>'[icon_img]',		'type'=>'[varchar](80)',	'param'=>'NULL', 					'constr'=>'',		'def'=>"" ),
array('col'=>'[isactive]',		'type'=>'[bit]',			'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[islocked]',		'type'=>'[bit]',			'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(0) " )
);

$Ainsert=array(
array('naam'=>'TPT',	'identifier'=>'TPT','icon_img'=>'../image/user1-32.png','address'=>'Finlandweg 21','postalcode'=>'4538BL','city'=>'Terneuzen','tpto_country_id'=>1),
array('naam'=>'basf2',	'identifier'=>'BAS','icon_img'=>'../image/user2-32.png','address'=>'Zandstraat 33','postalcode'=>'1144XF','city'=>'Colpontier','tpto_country_id'=>2)
);

echo create_initial_create_table($t, $Astruct, 'ON [PRIMARY]');
echo create_initial_insert_records($t, $Ainsert);
}

Function create_initial_indigo_status($t)
{
$Astruct=array(
array('col'=>'[id]',			'type'=>'[int]',			'param'=>'IDENTITY(1,1) NOT NULL',	'constr'=>'',		'def'=>"" ),
array('col'=>'[naam]',			'type'=>'[varchar](8)',		'param'=>'NOT NULL',				'constr'=>'',		'def'=>"DEFAULT ('status')" ),
array('col'=>'[identifier]',	'type'=>'[varchar](1)',		'param'=>'UNIQUE NOT NULL',				'constr'=>'',		'def'=>"" ),
array('col'=>'[isactive]',		'type'=>'[bit]',			'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(1) " ),
array('col'=>'[islocked]',		'type'=>'[bit]',			'param'=>'NULL', 					'constr'=>'',		'def'=>" DEFAULT(1) " )
);

$Ainsert=array(
array('naam'=>'pending','identifier'=>'p'),
array('naam'=>'released','identifier'=>'r'),
array('naam'=>'finished','identifier'=>'f'),
array('naam'=>'on-hold','identifier'=>'o'),
array('naam'=>'history','identifier'=>'h')
);

echo create_initial_create_table($t, $Astruct, 'ON [PRIMARY]');
echo create_initial_insert_records($t, $Ainsert);
}



Function create_initial_insert_records($t, $struct)
{	$msg='';
	foreach($struct as $record)
	{
		$q= "INSERT INTO [dbo].[".$t."]";
		$cols = '('; $vals=' VALUES (';

		foreach($record as $col => $val)
		{	$cols.="". $col . ", ";
			$vals.="'". $val . "', ";
		}
		$q.=substr($cols,0,-2).') '.substr($vals,0,-2).') ';
		mssql_query($q);$msg.=$q. '<br />';
	}
	return $msg;
}

Function create_initial_create_table($t, $struct, $primary)
{
	if (create_initial_table_exists($t)) mssql_query("DROP TABLE [dbo].".$t);

	$q= "CREATE TABLE [dbo].[".$t."](";

	$collate = "COLLATE Latin1_General_CI_AS ";
	foreach($struct as $f)
	{
		$q.=' '. $f['col'];
		$q.=' '. $f['type'];
		if (substr($f['type'],0,7) == 'varchar' or substr($f['type'],0,4) == 'text') $q .= $collate;
		$q.=' '. $f['param'];
		$q.=' '. $f['constr'];
		$q.= $f['def'].', ';
	}
	echo $q=substr($q,0,-2).' ) '.$primary;
	mssql_query($q);

	return $q.'<br />';
}
function create_initial_table_exists($t)
{
    $r = mssql_query("SELECT * FROM sysobjects WHERE name = '" . $t . "'") ;
	if (mssql_fetch_row($r)) {
		return 1;
    }
	return 0;
}

?>
