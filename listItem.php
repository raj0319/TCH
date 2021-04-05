<?php
include_once("include/constant.php");
$prodCollection = isSet($_REQUEST["prodCollection"])?$_REQUEST["prodCollection"]:"";
if(!empty($prodCollection) && !is_numeric($prodCollection) && !(isSet($_REQUEST["strSearch"]) || isSet($_REQUEST["hotDeal"]) || isSet($_REQUEST["modernFurniture"]) || isSet($_REQUEST["DiscountedFurniture"]) || (isSet($_REQUEST["fromEmail"]) && $_REQUEST["fromEmail"] == "email") || isSet($_REQUEST["clearanceSale"]) || isSet($_REQUEST["hotBuy"]) || isSet($_REQUEST["colorCollection"]) || (isSet($_REQUEST["fastdeliveryfurn"]) && $_REQUEST["fastdeliveryfurn"] == "inStock")))
{
	$limitExplode = 2;
	$cateSupplierUrlArr = explode("by",$prodCollection,$limitExplode);
	// print_r($cateSupplierUrlArr);
	$cateColorUrl = $cateSupplierUrlArr[0];
	$supplier = isSet($cateSupplierUrlArr[1])?$cateSupplierUrlArr[1]:"";
	$colorVal = $categoryDesc = $idCategorySearch = "";
	
	$phpCachePath = GlobalVariable::phpcacheRootPath();
	if(!file_exists($phpCachePath.'/'."allCategoryList.php"))
	{
		$pIdCategoryStart = GlobalVariable::getCategoryStart(pIdStore);
		Products::getCategoryListFromParent($pIdCategoryStart,true);
	}
	include_once($phpCachePath.'/'."allCategoryList.php");
	$matchCateArr = array();
	function sortByLength($a,$b){
		return strlen($b)-strlen($a);
	}
	uasort($allCategoryArr,'sortByLength');
	foreach($allCategoryArr as $idCate=>$cateVal)
	{
		$catePos = stripos($cateColorUrl,$cateVal);
		if($catePos !== false)
		{
			$categoryDesc = $cateVal;
			$cateUrl = substr($cateColorUrl,$catePos);
			$cateUrl = trim($cateUrl);
			if($cateUrl == $cateVal)
			{
				$categoryDesc = $cateUrl;
				$idCategorySearch = $idCate;
				$colorVal = str_replace($cateUrl,"",$cateColorUrl);
				$colorVal = trim($colorVal);
				break;
			}
		}
	}
	if(empty($categoryDesc)){
		$colorVal = $cateColorUrl;
	}
	if(!empty($supplier) || !empty($idCategorySearch) || !empty($colorVal))
	{
		$isCacheReqUri = true;
	}
	if(!empty($colorVal))
	{
		$colorId = Functions::getMatchedColorID($colorVal);
		$_REQUEST['color'] = $colorId;
	}
	$_REQUEST["supplierDesc"] = $supplier;
	$_REQUEST['idCategorySearch'] = $idCategorySearch;
	// echo "supplier -> ".$supplier."<br>";
	// echo "colorVal -> ".$colorVal."<br>";
	// echo "categoryDesc -> ".$categoryDesc."<br>";
	// echo "idCategorySearch -> ".$idCategorySearch."<br>";
}
// exit;
if(isSet($_REQUEST["idCategorySearch"]))
{
	$idCategorySrch = Functions::getRequest("idCategorySearch");
	//$idCategorySrch = $_REQUEST["idCategorySearch"];
	$idCategorySrch = strtolower($idCategorySrch);
	// $idCategorySrch = str_replace('and', '&', $idCategorySrch);
	
	if (!is_numeric($idCategorySrch))
	{
		$idCategorySrch = Categories::getCategoryId($idCategorySrch,"",true);
	}
	$categoryList = Categories::getCategoriesList(-1,$idCategorySrch);
	if(count($categoryList)<=0)
	{
		unset ($_REQUEST['idCategorySearch']);
		$_REQUEST["idCategory"]=$idCategorySrch;
	}
	else
	{
		$pIdCategoryParentSearch = Functions::getRequest("idCategorySearch");
		If(!ctype_digit($pIdCategoryParentSearch))
		{
			$pIdCategoryParentSearch = preg_replace("/[^0-9]/","",$pIdCategoryParentSearch);
		}
		if($pIdCategoryParentSearch > 2147483647){
			unset($_REQUEST['idCategorySearch']);
		}
	}
}
$backofficeLogin = false;
if(Functions::getSessionVariable("backofficeLogin",""))
{
	$backofficeLogin = true;
}	
$brand = array();
$brand = $_SERVER['REQUEST_URI'];
$brand = explode('/',$brand);
$categoryURL = false;
if(isSet($_REQUEST["hotDeal"]) || isSet($_REQUEST["BlackFridayDeals"]) || isSet($_REQUEST["CyberMondayDeals"]))
{
	$_REQUEST["hotDeal"] = "-1";
}
else if(isSet($_REQUEST["hotBuy"]))
{
	if($_REQUEST["hotBuy"] != "-1")
	{
		$_REQUEST["hotBuy"] = "-1";
	}
}
else if(isSet($_REQUEST["clearanceSale"]))
{
	if($_REQUEST["clearanceSale"] != "true")
	{
		$_REQUEST["clearanceSale"] = "true";
	}
}
else if(isSet($_REQUEST["modernFurniture"]))
{
	if($_REQUEST["modernFurniture"] != 1)
	{
		$_REQUEST["modernFurniture"] = "1";
		$categoryURL = true;
		// unset($_REQUEST["modernFurniture"]);
	}
}
else if(isSet($_REQUEST["DiscountedFurniture"]))
{
	if($_REQUEST["DiscountedFurniture"] != 1)
	{
		$_REQUEST["DiscountedFurniture"] = "1";
		$categoryURL = true;
		// unset($_REQUEST["modernFurniture"]);
	}
}
else if(in_array('Brand',$brand))
{
	$categoryURL = true;
}
else if(isSet($_REQUEST["colorCollection"]))
{
	if($_REQUEST["colorCollection"] != "-1")
	{
		$_REQUEST["colorCollection"] = "-1";
	}
}
else if(isSet($_REQUEST["latestCollection"]))
{
	$_REQUEST["shortByField"] = "newArrive";	
	$_REQUEST["idCategorySearch"] = GlobalVariable::getcategoryStart(pIdStore);	
}
$ProductDetails = array();
$googleItem = Functions::getRequest("item");
if(!empty($googleItem))
{
	$pIdProduct = Products::getProductId($googleItem);
	if(!empty($pIdProduct))
	{
		$ProductDetails = Products::getProductDetails($pIdProduct,true,false,true);
		if(!empty($ProductDetails))
		{
			$prodCategory = $ProductDetails->prodCategory;
			if(!empty($prodCategory))
			{
				$_REQUEST["idCategory"] = $prodCategory;
			}
		}
	}
}
$command = Functions::getRequest("command");
$fastdeliveryfurn = Functions::getRequest("fastdeliveryfurn");
if(!(isSet($_REQUEST["idCategorySearch"]) || isSet($_REQUEST["idCategory"]) || isSet($_REQUEST["idSupplier"]) || isSet($_REQUEST["supplierDesc"]) || isSet($_REQUEST["strSearch"]) || isSet($_REQUEST["hotDeal"]) || isSet($_REQUEST["modernFurniture"]) || isSet($_REQUEST["DiscountedFurniture"]) || (isSet($_REQUEST["fromEmail"]) && $_REQUEST["fromEmail"] == "email") || isSet($_REQUEST["clearanceSale"]) || isSet($_REQUEST["hotBuy"]) || isSet($_REQUEST["colorCollection"]) || $fastdeliveryfurn == "inStock"))
{
	$link = Functions::createListOneCateURL(279,"The Classy Home");
	header("Location: ".$link);
	killProcess();
}
if(isSet($_REQUEST["strSearch"]))
{
	if(strlen($_REQUEST["strSearch"]) > 0 )
	{
		$_REQUEST["strSearch"] = trim($_REQUEST["strSearch"]);
	}
	$strSearchText = trim(Functions::getRequest("strSearch"));
	if(Functions::getRequest("autoSelected") == "yes")
	{
		$url = Products::getProductUrlFromSearchString($strSearchText);
		if(!empty($url))
		{
			header("Location: ".$url);
			killProcess();
		}
	}
	
	$searchResultUrlArr = GlobalVariable::getSearchResultUrlArr();
	$strSearchText1 = trim(preg_replace('!\s+!', ' ', $strSearchText));
	if(isSet($searchResultUrlArr[strtolower($strSearchText1)]))
	{
		header("Location: ".$searchResultUrlArr[strtolower($strSearchText1)]);
		killProcess();
	}
	if(strpos(strtolower($strSearchText),'gift') !== false)
	{
		header("Location: /GiftCard.php");
		killProcess();
	}
	
	$listOneCategoryURL = "";
	$listOneCategoryURL = Functions::getSupNameFromSearch($strSearchText);
	if(empty($listOneCategoryURL))
	{
		$listOneCategoryURL = Functions::checkAndGetCategoryUrl($strSearchText);
	}
	
	if($listOneCategoryURL != "")
	{
		header("Location: $listOneCategoryURL");
		killProcess();
	}

}
$getSupplierDesc = Functions::getRequest("supplierDesc");
if(!empty($getSupplierDesc))
{
	$getidSupplier = Suppliers::getSupplierId($getSupplierDesc);
	if(!empty($getSupplierDesc) && $getidSupplier == "")
	{
		$getidSupplier = Suppliers::getSupplierIdWithLike($getSupplierDesc);
	}
	if(!empty($getSupplierDesc))
	{
		$_REQUEST["idSupplier"] = $getidSupplier;
	}
}
$idCate = "";
$getidCategory = "";
$idCategoryParent = "";
$getChildIdCategory = Functions::getRequest("idCategory");
$getChildIdCategory = Functions::urlDecodeCategory($getChildIdCategory);
$getParentIdCategory = Functions::getRequest("parentIdCategory");
$getParentIdCategory = Functions::urlDecodeCategory($getParentIdCategory);
$googleItem = Functions::getRequest("item");
// $getParentIdCategory = str_replace(",","&#44",$getParentIdCategory); //comment in live
$cateDetail = "";
$requestARR = $_REQUEST;
if(!isSet($_REQUEST["strSearch"]) && isSet($_REQUEST["idCategorySearch"]))
{
	$idCategoryParent = Functions::getRequest("idCategorySearch");
	//$idCategoryParent = $_REQUEST["idCategorySearch"];
	$idCategoryParent = strtolower($idCategoryParent);
	// $idCategoryParent = str_replace('and', '&', $idCategoryParent);
	if (!is_numeric($idCategoryParent))
	{
		$idCategoryParent = Categories::getCategoryId($idCategoryParent);
		$_REQUEST["idCategorySearch"] = $idCategoryParent;
		$idCate = $idCategoryParent;
	}
	else
		$idCate = $idCategoryParent;
	
}
$fastdeliveryfurn = Functions::getRequest("fastdeliveryfurn");
if(isset($fastdeliveryfurn) && $fastdeliveryfurn=='inStock')
{
	array_merge($requestARR,array("fastdeliveryfurn"=>"inStock"));
}
if(!empty($getChildIdCategory) && !empty($getParentIdCategory) && !is_numeric($getChildIdCategory))
{
	$childCategoryId = Categories::getCategoryId($getChildIdCategory);
	$parentCategoryId = Categories::getCategoryId($getParentIdCategory);
	$getidCategory = Categories::getChildCategory($parentCategoryId,$getChildIdCategory);
	$cateDetail = Categories::getCategoryInfo($getidCategory);
	$requestARR["idCategory"] = $getidCategory;
	$requestARR = Functions::filterRequestForDB($requestARR);
	$_REQUEST = $requestARR;
	if(empty($getidSupplier))
	{
		$getidSupplier = Functions::getRequest("idSupplier");
	}
	if(!empty($getSupplierDesc))
	{
		$requestARR["idSupplier"] = $getidSupplier;
	}
	
	$idCate = $getidCategory;
}
else
{
	$_REQUEST = Functions::filterRequestForDB($_REQUEST);
	$requestARR = $_REQUEST;
	if(empty($getidSupplier))
	{
		$getidSupplier = Functions::getRequest("idSupplier");
	}
	if(!empty($getSupplierDesc))
	{
		$requestARR["idSupplier"] = $getidSupplier;
	}
	$requestQueryString = $_REQUEST;
	$getidCategory = Functions::getRequest("idCategory");
	if(!empty($getidCategory))
	{
		$idCate = $getidCategory;
		if($idCate != "" && !is_null($idCate))
		{
			if(strpos($idCate,",") > 0)
			{
				$idCate = explode(",",$idCate);
				$idCate = $idCate[0];
			}
		}
	}
	$cateDetail = Categories::getCategoryInfo($idCate);
	if(array_key_exists("idCategory",$requestQueryString) && sizeof($requestQueryString) == 1 && ctype_digit($idCate))
	{
		if(!empty($cateDetail))
		{
			$pIdParentCategory =  $cateDetail->idParentCategory;
			$categoryDescription = $cateDetail->categoryDesc;
			$categoryUrl1 = Functions::createListItemCategoryUrl($pIdParentCategory,$categoryDescription);
			if(!empty($categoryUrl1))
			{
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: ".$categoryUrl1);
				killProcess();
			}
		}
	}
	if(array_key_exists("idSupplier",$requestQueryString) && sizeof($requestQueryString) == 1 && ctype_digit($getidSupplier))
	{
		$supplierData = GlobalVariable::getSupplierArray($getidSupplier);
		if(!empty($supplierData))
		{
			$supplierName = $supplierData["supplierName"];
			if(!empty($supplierName))
			{
				$supplierName = str_replace(" ","+",$supplierName);
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: /Brand/".$supplierName);
				killProcess();
			}
		}
	}
}
if($getParentIdCategory != "" && empty($idCate))
{
	$link = Functions::createListOneCateURL(279,"The Classy Home");
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: ".$link);
	killProcess();
}

if((!empty($getChildIdCategory) && !empty($getParentIdCategory) && !is_numeric($getChildIdCategory)) || (!empty($idCate) && !strpos(strtolower($_SERVER['REQUEST_URI']),'listproduct') && !(isSet($command) && $command == 'inStock')) || (isSet($_REQUEST["clearanceSale"]) || isSet($_REQUEST["hotBuy"]) || isSet($_REQUEST["hotDeal"]) || isSet($_REQUEST["modernFurniture"]) || isSet($_REQUEST["DiscountedFurniture"]) || (isSet($fastdeliveryfurn) && $fastdeliveryfurn == 'inStock')))
{
	$categoryURL = true;
	if(((isSet($_REQUEST["clearanceSale"]) || isSet($_REQUEST["hotBuy"]) || isSet($_REQUEST["hotDeal"]) || isSet($_REQUEST["modernFurniture"]) || isSet($_REQUEST["DiscountedFurniture"]) || (isSet($fastdeliveryfurn) && $fastdeliveryfurn == 'inStock')) && isSet($_REQUEST["page"])))
	{
		$categoryURL = false;
	}
}

$pCompany = Functions::getSessionVariable("pCompany","");
$pCompany = Functions::removeHiddenCharacter($pCompany);
$pHeaderKeywords = Functions::getSessionVariable("pHeaderKeywords","");
$pHeaderKeywords = Functions::removeHiddenCharacter($pHeaderKeywords);
$supplierName = "";
$pMetaDescription = "";
$pSearchKeywords = "";
$supplierTitleDesc = "";
$supplierMetaDesc = "";
$supplierKeyword = "";
$supplierDetails = "";
$supplierId = "";
$categoryTitleDesc = "";
if(!empty($getidSupplier))
{
	$supplierId = $getidSupplier;
	if($supplierId != "" && !is_null($supplierId))
	{
		if(strpos($supplierId,",") > 0)
		{
			$supplierId = explode(",",$supplierId);
			$supplierId = $supplierId[0];
		}
	}
	$data = GlobalVariable::getSupplierArray($supplierId);
	if(!empty($data))
	{
		if(intval($data["active"]) == -1)
		{
			$supplierName = $data["supplierName"];
			$supplierTitleDesc = $data["titleDescription"];
			$supplierMetaDesc = $data["metaDescription"];
			$supplierKeyword = $data["Keywords"];
			$supplierDetails = $data["Details"];
			$pMetaDescription = $supplierName." - ".$supplierDetails;
			$pSearchKeywords = Functions::removeHiddenCharacter($supplierKeyword);
		}
	}
}
$listItemTitle = "";
$listItemMetaDesc = "";
$listItemKeywords = "";
$categoryDescription = "";
$categoryMetaDesc = "";
$categoryKeyword  = "";
$colorName = "";
$pColor = "";
$pColorId = Functions::getRequest("color");
if(Functions::checkValidNumCheck($pColorId))
{
	$pColor = Functions::getMatchingColors($pColorId);
}
$pStyle = Functions::getRequest("style");
$pStyle = Functions::removeHiddenCharacter($pStyle);
if(!empty($idCate))
{
	if(empty($cateDetail))
	{
		$cateDetail = Categories::getCategoryInfo($getidCategory);
	}
	if(!empty($cateDetail))
	{
		$catDetails = $cateDetail->details;
		$categoryDescription = $cateDetail->categoryDesc;
		$categoryDescription = Functions::removeHiddenCharacter($categoryDescription);
		$categoryTitleDesc = $cateDetail->titleDescription;
		$categoryMetaDesc = $cateDetail->metaDescription;
		$categoryKeyword = $cateDetail->keywords;
		//get parent category
		$pIdParentCategory =  $cateDetail->idParentCategory;
		
		if(!empty($pIdParentCategory))
		{
			$pMetaDescription = Functions::removeHiddenCharacter($catDetails);
			$pSearchKeywords = Functions::removeHiddenCharacter($categoryKeyword);
		}
	}
}
If(!empty($getidSupplier))
{
	// $listItemTitle = Functions::removeHiddenCharacter($supplierTitleDesc);
	$listItemTitle = $supplierName." Products | The Classy Home";
	$listItemMetaDesc = Functions::removeHiddenCharacter($supplierMetaDesc);
	$listItemKeywords = Functions::removeHiddenCharacter($supplierKeyword);
}
If(!empty($idCate))
{
	$listItemTitle = Functions::removeHiddenCharacter($categoryTitleDesc);
	$listItemMetaDesc = Functions::removeHiddenCharacter($categoryMetaDesc);
	$listItemKeywords = Functions::removeHiddenCharacter($categoryKeyword);
}

If(!empty($pColor))
{
	$pMetaDescription = $pMetaDescription." ".$pColor;
	$pSearchKeywords = $pSearchKeywords." ".$pColor;
}

If(!empty($supplierName))
{
	$pMetaDescription = $pMetaDescription." ".$supplierName;
	$pSearchKeywords = $pSearchKeywords." ".$supplierName;
}

If(!empty($pStyle))
{
	$pMetaDescription = $pMetaDescription." ".$pStyle;
}

$pMetaDescription =Functions::removeHiddenCharacter($pMetaDescription);
$colorName = "";
if(!empty($idCate))
{
	$colorName = Functions::getColorName($pColorId)." ";
	$colorName = Functions::removeHiddenCharacter($colorName);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
	if(strtolower($getChildIdCategory) == "vanities")
	{
	?>
	<script type="application/ld+json">{"@context":"http://schema.org","@type":"WebSite","name":"TheClassyHome","url":"https://www.TheClassyHome.com"}</script>
  
    <script type="application/ld+json">{"@context":"http://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":0,"item":{"@id":"https://www.theclassyhome.com/category/Bedrooms/Vanities","name":"Vanity Set"}},{"@type":"ListItem","position":1,"item":{"@id":"https://www.theclassyhome.com/category/Bedrooms/Vanities","name":"Bedroom Vanity"}}]}</script>
	<?php
	}
	if(strtolower($getChildIdCategory) == "living room sets")
	{
	?>
	<script type="application/ld+json">{"@context":"http://schema.org","@type":"WebSite","name":"TheClassyHome","url":"https://www.TheClassyHome.com"}</script>
  
    <script type="application/ld+json">{"@context":"http://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":0,"item":{"@id":"https://www.theclassyhome.com/category/Living+Rooms/Living+Room+Sets","name":"cheap living room sets"}},{"@type":"ListItem","position":1,"item":{"@id":"https://www.theclassyhome.com/category/Living+Rooms/Living+Room+Sets","name":"cheap furniture sets"}},{"@type":"ListItem","position":2,"item":{"@id":"https://www.theclassyhome.com/category/Living+Rooms/Living+Room+Sets","name":"elegant living room sets"}}]}</script>
	<?php
	}
	if(strtolower($getChildIdCategory) == "metal beds")
	{
	?>
	<script type="application/ld+json">{"@context":"http://schema.org","@type":"WebSite","name":"TheClassyHome","url":"https://www.TheClassyHome.com"}</script>
  
    <script type="application/ld+json">{"@context":"http://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":0,"item":{"@id":"https://www.theclassyhome.com/category/Bedrooms/Metal+Beds","name":"Metal beds"}}]}</script>
	<?php
	}
	if(strtolower($getChildIdCategory) == "master bedrooms")
	{
	?>
	<script type="application/ld+json">{"@context":"http://schema.org","@type":"WebSite","name":"TheClassyHome","url":"https://www.TheClassyHome.com"}</script>
  
    <script type="application/ld+json">{"@context":"http://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":0,"item":{"@id":"https://www.theclassyhome.com/category/Bedrooms/Master+Bedrooms","name":"Master Bedroom Furniture"}}]}</script>
	<?php
	}
	if(strtolower($getSupplierDesc) == "acme furniture")
	{
	?>
	<script type="application/ld+json">{"@context":"http://schema.org","@type":"WebSite","name":"TheClassyHome","url":"https://www.TheClassyHome.com"}</script>
  
    <script type="application/ld+json">{"@context":"http://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":0,"item":{"@id":"https://www.theclassyhome.com/Brand/Acme+Furniture","name":"acme furniture"}}]}</script>
	<?php
	}
	if(strtolower($getChildIdCategory) == "bunk beds")
	{
	?>
	<script type="application/ld+json">{"@context":"http://schema.org","@type":"WebSite","name":"TheClassyHome","url":"https://www.TheClassyHome.com"}</script>
  
    <script type="application/ld+json">{"@context":"http://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":0,"item":{"@id":"https://www.theclassyhome.com/category/Kids/Bunk+Beds","name":"Bunk Beds"}}]}</script>
	<?php
	}
	if(strtolower($getChildIdCategory) == "benches" && strtolower($getParentIdCategory) == "kitchen, dining & bars")
	{
	?>
	<script type="application/ld+json">{"@context":"http://schema.org","@type":"WebSite","name":"TheClassyHome","url":"https://www.TheClassyHome.com"}</script>
  
    <script type="application/ld+json">{"@context":"http://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":0,"item":{"@id":"https://www.theclassyhome.com/category/Kitchen,+Dining+And+Bars/Benches","name":"counter height bench"}}]}</script>
	<?php
	} 

if(isSet($_REQUEST["hotDeal"]) && $_REQUEST["hotDeal"]==-1)
{
?>
	<title>Get your desired furniture with hot deals at The Classy Home</title>
	<meta property="description" name="DESCRIPTION" content="The easiest way to your furniture by which you'll have reasonable price products. Choose your favorite furniture from hot deals at The Classy Home." />
	 
<?php	
}
else if(isSet($_REQUEST["hotBuy"]) && $_REQUEST["hotBuy"]==-1)
{
?>
	<title>Best Buy Furniture | Hot Buy | The Classy Home</title>
	<meta property="description" name="DESCRIPTION" content="Find trending products of the Classy Home store in our Best Buy Furniture or Hot Buy section and shop it to give a rich look to your home." />
<?php	
}
else if(isSet($_REQUEST["modernFurniture"]) && $_REQUEST["modernFurniture"]== 1)
{
?>
	<title>New Style Furniture | Modern Furniture | The Classy Home</title>
	<meta property="description" name="DESCRIPTION" content="Be modern with the modern furniture collection at The Classy Home where you will come across the best products at the best price. Modernize your home with modern furniture." />
<?php	
}
else if(isSet($_REQUEST["DiscountedFurniture"]) && $_REQUEST["DiscountedFurniture"]== 1)
{
?>
	<title>Special Discount | Discounted Furniture | The Classy Home</title>
	<meta property="description" name="DESCRIPTION" content="Decor your dream home with home decor furniture items. Shop furniture at the discounted price only from The Classy Home now. Free shipping in NYC*." />
<?php	
}
else if(isSet($_REQUEST["colorCollection"]) && $_REQUEST["colorCollection"]== -1)
{
?>
	<title>Enhance the beauty of your home with colors - The Classy Home</title>
	<meta property="description" name="DESCRIPTION" content="Every color has its own beauty and that's what make it beautiful in every way. The classy home has the best color collection for the best picked products." />
<?php	
}
else if(isSet($_REQUEST['fastdeliveryfurn']) && $_REQUEST['fastdeliveryfurn']=='inStock')
{ ?>
	<title>Buy Ready to Ship Furniture Products Online | The Classy Home</title>
	<meta property="description" name="DESCRIPTION" content="Looking for the local furniture store around you to get fast delivery? The Classy Home brings you the ready to ship products with the fast delivery option, so shop online." />
<?php
}
else
{
	if(isSet($_REQUEST["clearanceSale"]) && $_REQUEST["clearanceSale"]=="true")
	{
		?>
		<title>Stock Clearance Sale</title>
		<?php
	}
	if(!empty($googleItem) && !empty($ProductDetails))
	{
		$prodDescription = $ProductDetails->description;
		$prodDescription = Functions::removeHiddenCharacter($prodDescription);
		$prodDescription = htmlentities($prodDescription,ENT_QUOTES);
		if(!empty($prodDescription))
		{
			$pDescriptionMeta = $prodDescription;
		}
		if(!empty($ProductDetails->htmlTitle))
		{?>
			<title data-itemprop="title"><?php echo $ProductDetails->htmlTitle;?></title>
		<?php
		}
		else
		{?>
			<title data-itemprop="title"><?php echo $pDescriptionMeta;?></title>
		<?php
		}
	}
	
	$categoryDescription = htmlentities($categoryDescription,ENT_QUOTES);
	$pMetaDescription = htmlentities($pMetaDescription,ENT_QUOTES);
	if(!empty($listItemMetaDesc))
	{
		$listItemMetaDesc = htmlentities($listItemMetaDesc,ENT_QUOTES);
		if(!empty($listItemMetaDesc))
		{
			$listItemMetaDesc = $colorName." ".$listItemMetaDesc;
		}
		else
		{
			$listItemMetaDesc = $colorName.$categoryDescription." ".$listItemMetaDesc;
		}
	?>
		<meta property="description" name="DESCRIPTION" content="<?php echo $listItemMetaDesc;?>" />
	<?php
	}
	else if(empty($idCate) && empty($pStyle) && empty($supplierName) && empty($pColor))
	{?>
		<meta property="description" name="DESCRIPTION" content="We the classy home provide furniture of different category like Living Rooms Furniture, Bedrooms Furniture, Entertainment Furniture, Home Office Furniture, Kids Furniture and many more" />
	<?php
	}
	else
	{?>
		<meta property="description" name="DESCRIPTION" content="<?php echo $pMetaDescription.", Furniture, ".$pCompany?>" />
	<?php
	}	
}
?>

<?php 
	include_once("headTag.php");
	$cssMinFileHeader = array();
	array_push($cssMinFileHeader, GlobalVariable::$cssPath."header.css");
	$cssFileHeader = Functions::createCssORJs("header-desktop.css",$cssMinFileHeader);
?>
<link rel='stylesheet' type='text/css' href='<?=$cssFileHeader;?>' />
<style>
<?php // include_once($_SERVER["DOCUMENT_ROOT"]."/headerStyle.php"); ?>

/* Landing Page CSS Start */
.LandingProduct .suppDiscProd.BadgeLeft {
	color: #FFF;
	font-weight: 500;
	padding: 3px 6px;
	display: inline-block;
	text-transform: uppercase;
	font-size: 13px;
	left: 5px;
	top: 5px;
	z-index: 1;
}

.LandingProduct .suppDiscProd {
	background: #38a6ec;
	top: 30px;
}

.LandingProduct .suppDiscProd sup {
	text-transform: none;
}

.LandingProduct {
	padding: 20px;
	margin-bottom: 30px;
	box-shadow: inset 0 0 10px 0 #EDEDED;
	-webkit-box-shadow: inset 0 0 10px 0 #EDEDED;
	-moz-box-shadow: inset 0 0 10px 0 #EDEDED;
	-o-box-shadow: inset 0 0 10px 0 #EDEDED;
	background: #fff
}

.LandingProduct .ProductThumb {
	text-align: center;
	position: relative;
	max-width: 600px;
}

.LandingProduct .ProductThumb .landingPageOtherImageSlider .item {
	max-width: 80px;
}

.LandingProduct .ProductContent {
	padding-left: 2%
}

.LandingProduct h1 {
	font-size: 20px;
	font-weight: 500;
	margin: 0 0 10px
}

.LandingProduct h1 a {
	color: #444
}

.LandingProduct .ManufecReview {
	font-size: 12px
}

.LandingProduct .ManufecReview i {
	color: #d52b2a;
	margin: 0 1px;
	font-size: 16px
}

.LandingProduct .ManufecReview span {
	font-size: 14px;
	margin-right: 15px
}

.LandingProduct .ManufecReview span a {
	color: #444;
	margin: 0 5px 0 2px;
	text-decoration: underline
}

.LandingProduct .ManufecReview span a:hover {
	color: #d52b2a;
	text-decoration: none
}

.LandingProduct .ManufecReview a:hover {
	color: #444
}

.LandingProduct .LandingPricing {
	padding: 15px 0
}

.LandingProduct .LandingPricing b {
	font-weight: 700;
	font-size: 22px;
	padding-left: 10px
}

.LandingProduct .LandingPricing span.strike-price-text {
	font-weight: 500;
	text-decoration: line-through;
	color: #909090;
	font-size: 18px
}

.LandingProduct .LandingPricing span.strike-price-text b {
	font-weight: 400;
	font-size: 16px;
	padding: 0
}

.LandingProduct .StockBouns {
	padding: 10px 0;
	border-top: 1px solid #f4f4f4;
	border-bottom: 1px solid #f4f4f4
}

.LandingProduct .StockBouns .Availability {
	/*width: 60%;
	float: left*/
}

.LandingProduct .StockBouns .BounsPoint {
	width: 40%;
	float: right;
	text-align: right;
	font-size: 13px
}

.LandingProduct .StockBouns b {
	font-weight: 700
}

.LandingProduct .StockBouns span {
	color: #0baf2a;
	font-weight: 700;
	margin: 0 2px
}

.LandingProduct .StockBouns i {
	font-size: 12px;
	color: #909090
}

.LandingProduct .StockBouns .BounsPoint b {
	margin: 0 2px
}

.LandingProduct .StockBouns .BounsPoint span {
	background: #3b5998;
	color: #FFF;
	width: 18px;
	height: 18px;
	line-height: 18px;
	text-align: center;
	display: inline-block;
	border-radius: 50%;
	position: relative
}

.LandingProduct .StockBouns .BounsPoint .popup {
	background: #444;
	position: absolute;
	right: 0;
	top: 28px;
	width: 210px;
	padding: 5px;
	font-weight: 400;
	font-size: 12px;
	line-height: 16px;
	border-radius: 8px;
	display: none
}

.LandingProduct .StockBouns .BounsPoint .popup:after {
	content: "";
	width: 0;
	height: 0;
	border-left: 8px solid transparent;
	border-right: 8px solid transparent;
	border-bottom: 8px solid #444;
	position: absolute;
	top: -7px;
	right: 5px
}

.LandingProduct .StockBouns .BounsPoint span:hover .popup {
	display: block
}

.LandingProduct .QTYCartBtn {
	border-bottom: 1px solid #f4f4f4;
	padding: 10px 0;
	text-align: right
}

.LandingProduct .QTYCartBtn .Quantity {
	display: inline-block;
	vertical-align: middle;
	margin-right: 10px
}

.LandingProduct .QTYCartBtn a.CartBTN {
	font-size: 16px;
	text-align: center;
	line-height: 50px;
	display: inline-block;
	vertical-align: middle
}

.LandingProduct .QTYCartBtn a.CartBTN span {
	float: left;
	height: 35px
}

.LandingProduct .SortDetail {
	font-size: 13px
}

.LandingProduct .SortDetail td {
	border-bottom: 1px solid #f4f4f4;
	padding: 8px 5px
}

.LandingProduct .SortDetail b {
	font-weight: 500;
	margin-right: 3px
}

.LandingProduct .SortDetail ul {
	margin: -4px 0 0;
	padding: 0;
	display: inline-block;
	vertical-align: top
}

.LandingProduct .SortDetail ul li {
	list-style: none;
	padding: 0 0 0 15px;
	position: relative;
	height: 22px;
	line-height: 22px
}

.LandingProduct .SortDetail ul li:before {
	color: #d52b2a;
	content: "\f00c";
	font-family: FontAwesome;
	left: 0;
	position: absolute;
	top: 2px;
	font-size: 12px
}

.LandingProduct .FullDetailBTN {
	text-align: center;
	padding-top: 15px
}

.transition {
	-webkit-transition: top .4s linear;
	-moz-transition: top .4s linear;
	-ms-transition: top .4s linear;
	-o-transition: top .4s linear;
	transition: top .4s linear
}

.timeTo {
	color: #444!important;
	font-size: 14px!important;
	font-weight: unset!important;
	line-height: 115%
}

.DetailTop .OfferBelt span.timeTo span {
	padding: 0 2px;
	color: #444
}

.timeTo div {
	display: inline-block;
	overflow: hidden;
	position: relative
}

.timeTo ul {
	list-style-type: none;
	margin: 0;
	padding: 0;
	position: absolute;
	left: 3px
}

.OfferDealPrice {
	overflow: hidden;
	padding: 15px 0
}

.LandingProduct .OfferDealPrice .OfferBelt {
	float: left;
	position: relative;
	text-align: center;
	width: 50%
}

.LandingProduct .OfferBelt {
	background: #f6f6f6;
	border: 1px dashed #d52b2a;
	border-radius: 10px;
	padding: 6px 10px
}

.LandingProduct .OfferBelt span {
	border-radius: 15px;
	color: #d52b2a;
	display: inline-block;
	font-weight: 500;
	padding: 5px
}

.LandingProduct .OfferBelt b {
	margin: 0 4px
}

.LandingProduct .OfferBelt span.DealsEnd {
	background: rgba(0, 0, 0, 0);
	border-radius: 0;
	color: #444;
	display: block;
	font-weight: 400;
	line-height: 26px;
	margin: 0;
	padding: 0
}

.LandingProduct .OfferDealPrice .DealPrice {
	float: right;
	width: 44%
}

.LandingProduct .DealPrice {
	padding: 15px 0;
	text-align: center
}

.without-strike-text {
	display: block;
	font-size: 24px;
	font-weight: 700;
	text-decoration: none;
	color: #444
}

.LandingProduct .DealPrice span.strike-price-text {
	color: #909090;
	font-size: 18px;
	font-weight: 500;
	text-decoration: line-through
}

.LandingProduct .DealPrice b {
	display: block;
	font-size: 24px;
	font-weight: 700
}

.LandingProduct .QTYCartBtn .NoSalesTax {
	float: left
}

.discontinuedItem {
	background-color: red;
	color: #000;
	font-size: 30px;
	height: auto;
	opacity: .7;
	position: absolute;
	top: 50%;
	width: 100%
}

@media only screen and (min-width:1100px) and (max-width:1290px) {
	.LandingProduct .StockBouns .Availability,
	.LandingProduct .StockBouns .BounsPoint {
		width: 50%
	}
}

@media only screen and (min-width:1000px) and (max-width:1099px) {
	.LandingProduct .StockBouns .Availability,
	.LandingProduct .StockBouns .BounsPoint {
		width: 50%
	}
}
/* Landing Page CSS End */

.specialMsg { width: 100%; height: auto; background-color: #fff2cb; padding: 8px 5px; }
.SpecialOrderMsg{ color: #837858; padding: 3px 5px; font-size: 12px; font-weight: normal; font-style: italic; margin-left: 10px; }

.Breadcrumbs{padding:15px 0; font-size:13px;}
.Breadcrumbs span{color:#909090; padding:0 8px;}
.Breadcrumbs a{color:#909090;}
.Breadcrumbs a:hover{color:#d52b2a;}

#quick_look_btn{position: absolute; right: 50%; bottom: 42%; margin-right: -45px; display:none;}
.Product:hover #quick_look_btn {display:block;}

.SiteContainer.ListingPage{max-width:1500px;}
.SiteContainer.ListingPage .ProductList{width:79%;}
.SiteContainer.ListingPage .ListingProductRow .Product {max-width:295px;}
.SiteContainer.ListingPage .ListingProductRow .Product .ProductShadow{padding:5px;}
.SiteContainer.ListingPage .ListingProductRow .Product:hover .ProductShadow{padding:5px;}
.SiteContainer.ListingPage .ListingProductRow .Product .Thumb img{max-width:276px; max-height:210px;}
.SiteContainer.ListingPage .ListingProductRow .Product .CartShareWishlist a.CartBTN span{height:26px; width:40px; padding-top:8px;}
.SiteContainer.ListingPage .ListingProductRow .Product .CartShareWishlist a.CartBTN {font-size:14px; line-height:34px;}
.SiteContainer.ListingPage .ListingProductRow .Product .CartShareWishlist .Share,.SiteContainer.ListingPage .ListingProductRow .Product .CartShareWishlist .Wishlist {
    font-size: 20px;
    color: #e4e4e4;
    display: inline-block;
    margin-top: 5px;
    cursor: pointer;
    position: relative;
}

.SiteContainer.ListingPage .ListingProductRow .Product .CartShareWishlist .Share:hover,.SiteContainer.ListingPage .ListingProductRow .Product .CartShareWishlist .Wishlist:hover
{
	 color: #d52b2a;
}

.SiteContainer.ListingPage .ListingProductRow .Product .CartShareWishlist .Share .ShareList a{color:#e4e4e4; border:1px solid #e4e4e4; margin-bottom:3px; border-radius:50%; width:18px; height:18px; line-height:18px; text-align:center; display:block;}
.SiteContainer.ListingPage .ListingProductRow .Product .CartShareWishlist .Share .ShareList a i{line-height:20px; font-size:12px;}


/* Filterbar */
.FilterBar{max-width:280px; width:22%;}
.FilterBar .FilterBlock{border:1px solid #f4f4f4; border-left-color:#d52b2a; padding:15px 0px 15px 15px; margin-bottom:15px; overflow:hidden; background: #fff}
.FilterBar .FilterBlock h4{font-weight:700; text-transform:uppercase; font-size:16px; margin:0 15px 0 0;}
.FilterBar .FilterBlock a h4{color:#444;}
.FilterBar .FilterBlock a h4 i{float:right;}
.FilterBar .FilterBlock .CustomScroll{padding-bottom:15px;}

.FilterBar .List{text-align:center; padding-top:15px;}
.FilterBar .List ul{margin:0 15px 0 0; padding:0; text-align:left;}
.FilterBar .List ul li{list-style:none; border-bottom:1px solid #f4f4f4; padding-left:28px; padding-bottom:3px; margin-bottom:2px;}
.FilterBar .List ul li:last-child{margin-bottom:0;}
.FilterBar .List ul li i{float:right; font-size:11px;}

.FilterBar label {position:relative; font-weight:normal; font-size:13px;}
.FilterBar input[type="checkbox"]{display:none;}
.FilterBar span:before,
.FilterBar span:after {content:''; position:absolute; top:5px; left:-25px; margin:auto;}
.FilterBar span.checkbox:before {width:20px; height:20px; background-color:#f6f6f6; left:-28px; box-sizing:border-box; border:1px solid #eaeaea; transition:border-color .2s;}
.FilterBar span.checkbox::after {content: '\f00c'; font-family: 'FontAwesome'; left:-24px; top:7px; color:transparent; transition:color .2s;}
.FilterBar input[type="checkbox"]:checked + label span.checkbox::after {color:#444;}

.CategoryDesc{font-size:13px; color:#909090; padding-bottom:5px;}
.CategoryDesc p{margin-top:0; line-height:20px;}
.CategoryDesc p a{padding-left:5px;}
.CategoryDesc .categoryReadMore{margin-top:0; line-height:20px;}
.CategoryDesc .categoryReadMore a{padding-left:5px;}

/*.ProductList{width:76%;}*/
.ProductList h1 span{font-size:14px; color:#909090; padding-left:5px;}

.SortBy{border-top:1px solid #f4f4f4; border-bottom:1px solid #f4f4f4; padding:13px 0; margin-bottom:20px;}
.SortBy b{float:left;}
.SortBy ul{margin:0; padding:0; text-align:center;}
.SortBy ul li{list-style:none; display:inline-block; font-size:13px; text-transform:uppercase; position:relative;}
.SortBy ul li:hover:after{content:""; background:#d52b2a; height:1px; width:100%; position:absolute; left:0; bottom:-14px;}
.SortBy ul li:hover:before{content:""; width:0; height:0; border-left:6px solid transparent; border-right:6px solid transparent; border-top:6px solid #d52b2a; position:absolute; bottom:-19px; left:50%; margin-left:-3px;}
.SortBy ul li a{color:#444; padding:0 10px;}
.SortBy ul li:hover a{color:#d52b2a;}

.SortBy ul li.active:after{content:""; background:#d52b2a; height:1px; width:100%; position:absolute; left:0; bottom:-14px;}
.SortBy ul li.active:before{content:""; width:0; height:0; border-left:6px solid transparent; border-right:6px solid transparent; border-top:6px solid #d52b2a; position:absolute; bottom:-19px; left:50%; margin-left:-3px;}
.SortBy ul li.active a{color:#d52b2a;}


.ListingCategorySlide{overflow:hidden; padding:0 20px 50px 20px; height:82px;}
.ListingCategorySlide .owl-carousel .owl-stage-outer{overflow:visible;}
.ListingCategorySlide .item{width:80px; height:80px; border:1px solid #f4f4f4; position:relative;}
.ListingCategorySlide .item a {font-size:12px; color:#909090; text-align:center; background: #fff;display: block;}
.ListingCategorySlide .item span{font-size:12px; color:#909090; text-align:center; display:block; position:absolute; left:0; bottom:-35px; width:90px; height:30px; margin-left:-5px;}
.ListingCategorySlide .owl-carousel .owl-stage { position: relative; -ms-touch-action: pan-Y; }
.ListingCategorySlide .owl-carousel .owl-item { min-height: 1px; float: left; -webkit-backface-visibility: hidden; -webkit-touch-callout: none; }

.ListingProductRow{border-top:1px solid #f4f4f4; overflow:hidden; position:relative; padding-bottom:50px;}
.ListingProductRow:after{content:""; height:100%; width:1px; background:#FFF; position:absolute; right:1px; top:0; z-index:1;}
.ListingProductRow .Product{border-right:1px solid #f4f4f4; float:left; max-width:328px; width:100%; border-bottom: 1px solid #f4f4f4;}
/*.Product {max-width: 330px; position: relative; background: #fff; }*/

.Product .ProductShadow .BadgeLeft{color:#FFF; font-weight:500; padding:3px 6px; display:inline-block; text-transform:uppercase; font-size:13px; position:absolute; left:10px; top:10px; z-index:1;}
.Product .ProductShadow .BadgeRight{color:#FFF; font-weight:500; padding:3px 6px; display:inline-block; text-transform:uppercase; font-size:13px; position:absolute; right:10px; top:10px; z-index:1;}
.Product .ProductShadow .HotBuy{background:#a652e7;}
.Product .ProductShadow .HotDeal{background:#dd9b0c;}
.Product .ProductShadow .DailyDeal{background:#d76610;}
.Product .ProductShadow .Exclusive{background:#0baf2a;}
.Product .ProductShadow .FreeShipping{background:#d52b2a;}
.Product .ProductShadow .suppDiscProd{background:#38a6ec;}
.Product .ProductShadow .Off{background:#3b5998;}
.Product .ProductShadow .New{background:#9bb010;}

/*.Product .CartShareWishlist a.CartBTN,
.Product .CartShareWishlist span.CartBTN{max-width:174px; width:100%; margin:0 auto;cursor: pointer;}*/

.Product .CartShareWishlist a.CartBTN.ViewItemBTN{padding:0;}

.Product .CartShareWishlist a.CartBTN {font-size:16px; line-height:40px;}
.Product .CartShareWishlist a.CartBTN span{height:30px; width:40px;  padding-top:10px;}

.ListingPage .Product .SortDesc .PricingOption{height:22px; font-weight:400;}
.ListingPage .Product .SortDesc .PricingOption b{line-height:normal; font-size:14px;}
.ListingPage .Product .SortDesc .PricingOption .Save{padding:5px 2px;}
.ListingPage .Product .SortDesc .PricingOption .Price{font-size:13px;}

.ListingPage .Product .LookLoveStar{text-align:center; overflow:hidden; min-height:20px;}
.ListingPage .Product .LookLoveStar span{font-size:14px; color:#d52b2a; text-align:center; display:inline-block;}
.ListingPage .Product .LookLoveStar span i{margin:0 2px;}
.ListingPage .Product .LookLoveStar a.small{font-size:12px; font-weight:normal; color:#444; border:1px solid #FFF; background:#FFF;}
.ListingPage .Product .LookLoveStar a.small i{font-size:12px;}
.ListingPage .Product .LookLoveStar a.small:hover{border:1px solid #d52b2a; color:#d52b2a; background:#FFF;}
	
.Product{max-width:330px; position:relative;}
/*.Product .ProductShadow{padding:10px 10px 0 10px; position:relative;}*/
/*.Product:hover .ProductShadow{padding:10px 10px 0 10px; box-shadow:inset 0px 0px 10px 0px #EDEDED; -webkit-box-shadow:inset 0px 0px 10px 0px #EDEDED; -moz-box-shadow: inset 0px 0px 10px 0px #EDEDED; -o-box-shadow:inset 0px 0px 10px 0px #EDEDED;}*/

.Product .Thumb{height:210px; width:100%; text-align:center;}
/*.Product .Thumb img{vertical-align:middle; max-height:200px; max-width:290px;transform-style: unset;}*/
.Product .Thumb .helper{display:inline-block; height:100%; vertical-align:middle;}
.Product .SortDesc {text-align: center;}
.Product .SortDesc h4 a {color:#444;}
.Product .SortDesc h4 a:hover {color:#d52b2a;}
.ListingProductRow .Product .SortDesc h4 {font-weight:400; font-size:14px; padding:0 15px; height:38px; overflow: hidden; margin-bottom:0px;}

.CartBTN{background:#d52b2a; border-radius:5px; display:block; line-height:50px; text-align:center; max-width:220px; padding:0 15px 0 0; color:#FFF; overflow:hidden; font-size:17px; font-weight:700; text-transform:uppercase; text-shadow: 1px 1px 1px #444; -moz-box-shadow:inset 0 1px 2px #a12423; -webkit-box-shadow:inset 0 1px 2px #a12423; box-shadow:inset 0 1px 2px #a12423;}
.CartBTN span{float:left; background:#444; width:50px; height:35px; margin-right:10px; padding-top:15px; text-align:center; vertical-align:middle;}
.CartBTN:hover{background:#e92f2e; color:#FFF;}

.Product .CartShareWishlist a.CartBTN,
.Product .CartShareWishlist span.CartBTN{max-width:174px; width:100%; margin:0 auto;cursor: pointer;}
.Product .CartShareWishlist a.CartBTN {font-size:16px; line-height:40px;}
.Product .CartShareWishlist a.CartBTN span{height:30px; width:40px;  padding-top:10px;}

.Product .CartShareWishlist{padding:10px;}
/*.Product .CartShareWishlist .Share,
.Product .CartShareWishlist .Wishlist{font-size:24px; color:#e4e4e4; display:inline-block; margin-top:10px; cursor:pointer; position:relative;}*/
.Product .CartShareWishlist .Share:hover,
.Product .CartShareWishlist .Wishlist:hover{color:#d52b2a;}
.Product .CartShareWishlist .Share .ShareList{display:none; position:absolute; left:0; bottom:22px;}
.Product .CartShareWishlist .Share:hover .ShareList{display:block;}
/*.Product .CartShareWishlist .Share .ShareList a{color:#e4e4e4; border:1px solid #e4e4e4; margin-bottom:5px; border-radius:50%; width:24px; height:24px; line-height:22px; text-align:center; display:block;}*/
/*.Product .CartShareWishlist .Share .ShareList a i{line-height:24px; font-size:13px;}*/
label{text-align:left; display:block; font-weight:500; padding:5px 0;}
input[type="text"]{border:1px solid #ebebeb; width:96%; padding:0 2%; height:40px;}
input[type="password"]{border:1px solid #ebebeb; width:96%; padding:0 2%; height:40px;}
select{border:1px solid #ebebeb; width:96%; padding:0 2%; height:40px; line-height:40px;}
textarea{border:1px solid #eee; width:96%; padding:2%; height:80px;}
button{border:0; background:#d52b2a; color:#FFF; cursor:pointer; font-weight:700; height:42px; padding:0 10px;}
button:hover{background:#444;}

.collectionCompleteBTN{padding:10px; border-bottom:1px solid #f4f4f4;}

.affirm-ala-price {color: #d52b2a;font-weight: bold;}
.DealPrice .affirm-as-low-as {margin: 0;}
.affirm-as-low-as a {color: #d52b2a;}

</style>
<script>var hasListItemData = false; var hideSearch = false;</script>
<script>
	var intInsideFltrCnt = 0;
	var totalItemCnt = 0;
	var insideFltrArr = [];
	var filterNameArr = [];
	var impressionQueue = [];
</script>
<script type="text/javascript" src="<?=GlobalVariable::$jsPath."affirmCheckout.js"?>" ></script>
</head>
<body>
<?php
global $imagePathBrandImg;
if(!empty($ProductDetails))
{				
	$descriptionGoogle = $ProductDetails->description;
	$descriptionGoogle = Functions::removeHiddenCharacter($descriptionGoogle);
	$descriptionGoogle = htmlentities($descriptionGoogle,ENT_QUOTES);
	
	$detailsGoogle = $ProductDetails->details;
	$detailsGoogle = Functions::removeHiddenCharacter($detailsGoogle);
	$detailsGoogle = htmlentities($detailsGoogle,ENT_QUOTES);

	$supplierNameGoogle = $ProductDetails->supplierName;
	$supplierImageGoogle = $ProductDetails->supplierImage;
	$pPriceGoogle = $ProductDetails->pPrice;
	if($ProductDetails->showDealPrice == true || $ProductDetails->hasGroupDiscount)
	{
		$pPriceGoogle = $ProductDetails->dealPrice;
	}
	$pPriceGoogle = Functions::money($pPriceGoogle);
	$pisGroupMain = $ProductDetails->isGroupMain;
	$pisBundleMain = $ProductDetails->isBundleMain;
	$cateDescProd = "";
?>
	<script type="application/ld+json"> 
		{
			"@context": "http://schema.org",
			"@type": "Product",
			"name": "<?php echo $descriptionGoogle; ?>",
			"sku": "<?php echo $ProductDetails->sku; ?>",
			"image": "<?php echo domainUrlHTTP.GlobalVariable::$catalogPath.$ProductDetails->smallImageUrl; ?>",
			"description": "<?php echo $detailsGoogle; ?>",
			"url": "<?php echo domainUrlHTTP.$ProductDetails->encodeDescURL; ?>",
			"brand": {
				"@type": "Brand",
				"name": "<?php echo $supplierNameGoogle; ?>",
				"logo": "<?php echo $imagePathBrandImg.$supplierImageGoogle; ?>"
			}
			<?php
			if(!($pisBundleMain == -1 && ($pisGroupMain != -1 || is_null($pisGroupMain))))
			{
				$availability = "";
				if($ProductDetails->active == "-1")
				{
					$googleStock = $ProductDetails->googleStock;
					if($googleStock == "instock")
					{
						$availability = '"availability": "http://schema.org/InStock",';
					}
					else if($googleStock == "outofstock")
					{
						$availability = '"availability": "http://schema.org/PreOrder",';
					}
				}
				else
				{
					$availability = '"availability": "http://schema.org/Discontinued",';
				}
			?>
			,
			"offers": {
				"@type": "Offer",
				"price": "<?php echo ($pPriceGoogle); ?>",
				"priceCurrency": "USD",
				<?php echo $availability; ?>
				"itemCondition": "http://schema.org/NewCondition",
				"priceValidUntil":"<?php echo date('Y-m-d');?>",
				"url":"https://www.theclassyhome.com/offerZone.php"
			}
			<?php
			}
			if($ProductDetails->rateReview > 0 && $ProductDetails->totalReview > 0)
			{?>
			,
			"aggregateRating": {
				"@type": "AggregateRating",
				"ratingValue": "<?php echo $ProductDetails->rateReview; ?>",
				"ratingCount": "<?php echo $ProductDetails->totalReview; ?>"
			}
			<?php
			}
			?>
			<?php
			$BreadCrumbCatStringArr = $ProductDetails->BreadCrumbCatString;					
			if(isSet($BreadCrumbCatStringArr[count($BreadCrumbCatStringArr)-1]["categoryDesc"]))
			{
				$cateDescProd = $BreadCrumbCatStringArr[count($BreadCrumbCatStringArr)-1]["categoryDesc"];
			?>
			,"category": "<?php  echo $BreadCrumbCatStringArr[count($BreadCrumbCatStringArr)-1]["categoryDesc"]; ?>"
			<?php
			}
			?>
		}
		</script>
		
		<script type="application/ld+json">
		{
		  "@context": "http://schema.org",
		  "@type": "BreadcrumbList",
		  "itemListElement": [
			<?php
			$BreadCrumbCatStringArr = $ProductDetails->BreadCrumbCatString;
			$BreadCrumbCatArrCount = 0;
			foreach($BreadCrumbCatStringArr as $BreadCrumbCatArr)
			{
				$BreadCrumbCatArrCount++;
				if($BreadCrumbCatArrCount > 1)
				{
					echo ",";
				}
				?>
				{
					"@type": "ListItem",
					"position": <?php echo $BreadCrumbCatArrCount; ?>,
					"item": {
					  "@id": "http://www.theclassyhome.com<?php echo Functions::createListOneCateURL($BreadCrumbCatArr["idparentcategory"]); ?>",
					  "name": "<?php echo $BreadCrumbCatArr["categoryDesc"]; ?>"
					}
				  }
				<?php
			}
			?>
		  ]
		}
		</script>
		
		<?php
		if(isSet($ProductDetails->videoLink) && isSet($ProductDetails->videoUploadDateISO8601))
		{
			$videoLink = $ProductDetails->videoLink;
			$videoUploadDate = $ProductDetails->videoUploadDateISO8601;
			
			$videoLinkArr = preg_split( "/[=&]/", $videoLink);
			$videoLinkKey = "";
			if(isSet($videoLinkArr[1]))
			{
				$videoLinkKey = $videoLinkArr[1];
			}
			$videoThumbnailUrl = "http://img.youtube.com/vi/$videoLinkKey/default.jpg";
			$videoEmbedUrl = "http://www.youtube.com/embed/$videoLinkKey?loop=1&playlist='$videoLinkKey";
			if(!empty($videoLinkKey))
			{
		?>
		<script type="application/ld+json">
		{
		  "@context": "http://schema.org",
		  "@type": "VideoObject",
		  "name": "<?php echo $descriptionGoogle; ?>",
		  "description": "<?php echo $detailsGoogle; ?>",
		  "thumbnailUrl": "<?php echo $videoThumbnailUrl; ?>",
		  "uploadDate": "<?php echo $videoUploadDate; ?>",
		  "contentUrl": "<?php echo $videoLink; ?>",
		  "embedUrl": "<?php echo $videoEmbedUrl; ?>"
		}
		</script>
		<?php
			}
		} 
		if(!($pisBundleMain == -1 && ($pisGroupMain != -1 || is_null($pisGroupMain))) && $ProductDetails->active == "-1")
		{
		?>
		<script>		
		var google_tag_params = {
			ecomm_prodid: '<?php echo $ProductDetails->sku; ?>',
			ecomm_pagetype: 'product',
			ecomm_totalvalue: <?php echo ($pPriceGoogle); ?>,
			dynx_itemid: '<?php echo $ProductDetails->sku; ?>',
			dynx_pagetype: 'product',
			dynx_totalvalue: <?php echo ($pPriceGoogle); ?>,
			ecomm_category: '<?=$cateDescProd;?>',
		};
		</script>
	<?php
		}
}
 include_once("header.php");
 
 global $skipPageCache;
 $skipPageCache = true;
 global $hasCache;
 $hasCache = false;
 $hasListItemData = false;
?> 
<div class="stickyBodyContent">
<?php include_once($_SERVER["DOCUMENT_ROOT"]."/holidayHeader.php");	 ?>

  <section class="SiteContainer ListingPage">
    <div class="Breadcrumbs"><a href="/">Home</a>
     	<?php
		if($idCate != "" && !is_null($idCate))
		{
			$breadCrumbData = Functions::getBreadCrumbCategory($idCate);
			$breadCrumbDataListItem = $breadCrumbData;
			if(!empty($breadCrumbData))
			{
				$breadCrumbDataR = array_reverse($breadCrumbData);
				foreach($breadCrumbDataR as $bread)
				{
					if(!empty($bread))
					{
						$cateLink = "javascript:void(0)";
						if($bread["idparentcategory"] != $idCate)
						{
							$cateLink = Functions::createListOneCateURL($bread["idparentcategory"],$bread["categoryDesc"]);
							//$cateLink = Fu = "/ListOneCategory.php?idCategory=".$bread["idparentcategory"];
							if(isSet($_REQUEST['fastdeliveryfurn']) && $_REQUEST['fastdeliveryfurn']=='inStock')
							{
								$cateLink = "/category/FastDeliveryFurniture/".$bread["categoryDesc"];
								$cateLink = str_replace(" & "," And ",$cateLink);
								$cateLink = str_replace(" ","+",$cateLink);
								
								echo " <span>/</span> <a href='".$cateLink."' class='breadcrumb-link'>".$bread["categoryDesc"]."</a>";
							}
							else
							{
								echo " <span>/</span> <a href='".$cateLink."' class='breadcrumb-link'>".$bread["categoryDesc"]."</a>";
							}
						}
						else
						{
							echo " <span>/</span> ".$bread["categoryDesc"];
						}
					}
				}
			}
		}
		$GetstrSearch = Functions::getRequest("strSearch");
		$idCategorySearch = Functions::getRequest("idCategorySearch");
		if(!empty($GetstrSearch))
		{
			$searchString = $GetstrSearch;
			$searchString = Functions::stripTags($searchString);
			$searchString = substr($searchString, 0,40) . "...";
		?>
			<span>/</span> <a href="#" class="breadcrumb-link"><?php echo $searchString;?></a>
		<?php
		}else if(!empty($supplierName))
		{
			?>	
			<span>/</span> <a href="#" class="breadcrumb-link"><?php echo $supplierName;?></a>
			<?php
		}
		else if(isSet($_REQUEST["clearanceSale"]) && $_REQUEST["clearanceSale"]=="true")
		{
			?>	
			<span>/</span> <a href="#" class="breadcrumb-link">Clearance Sale</a>
			<?php
		}
       	?>
    </div>
	<?php if($backofficeLogin == true){?>
		<input type="hidden" value="<?php echo $backofficeLogin; ?>" id="backofficeLogin" /> 
	<?php } 
	$hideSearch = false;
	if(!empty($ProductDetails))
	{
		$hideSearch = true;
		?>
		<script>
		hideSearch = true;
		</script>
		<div class="list-item-detail-box" id="google-landing-item" data-item=<?=$googleItem;?>>
			<?php ListItemHTML::getLandingItemHtml($googleItem); ?>		
		</div>
		<?php
	}
	
	if(!empty($isCacheReqUri) && $isCacheReqUri){
		$categoryURL = true;
	}
	// $categoryURL = false;
	if($categoryURL){
		include_once('cachestart.php');
		$skipPageCache = false;
	}
	if(!$hasCache)
	{
		$hasPage = true;
		if(!empty($getSupplierDesc) && empty($getidSupplier) && stripos($_SERVER["REQUEST_URI"],"/brand/") !== false)
		{
			$strSearchText = Functions::getArrayValue($requestARR,"strSearch");
			if($strSearchText != "")
			{
				$strSearchText = "'".$strSearchText."'";
			}
			?>
			<div class="PageNotFound">
				<h1 class="StyleTitle">Oops, Product Not Found <br>
				</h1>
				<div class="PageNotFoundText" style="margin: 0 auto;max-width: 600px;padding: 3% 2% 6%;text-align: center;">
					<p><b><?=$strSearchText;?></b> Somebody really liked this page.<br>
					<br>
					Unfortunately it looks like someone really liked this page, and tore it out.
					But don‘t worry, there a lot of pages you can still see. Here is a little map to help you out: </p>
					<a href="javascript:window.history.back();" class="WRBTN">Go Back</a> 
				</div>
			</div>
			<script>
			if(document.getElementById("breadcrumb") != null && document.getElementById("breadcrumb").children.namedItem("breadcrumb-link") != null)
			{
				document.getElementById("breadcrumb").children.namedItem("breadcrumb-link").remove();
				document.getElementById("breadcrumb").innerHTML = document.getElementById("breadcrumb").innerHTML+"<a class='breadcrumb-link' href='javascript:void(0);'>Your search <?php  echo $strSearchText; ?> did not match any products</a>";
			}
			</script>
			<?php
			//ListItemHTML::listItemCustomerRecentView();
			?>
			<script>
				// $("#shortByBoxmain").hide();
			</script>
			<?php
			$hasPage = false;
		}
		if($hasPage)
		{
			$strSearch = Functions::getRequest("strSearch");
		
			$catTitle = "";
			if(!empty($isCacheReqUri))
			{				
				if(!empty($supplier))
				{
					if(!empty($categoryDesc) || !empty($colorVal)){
						$catTitle .= " by ".$supplier;
					}
					else{
						$catTitle .= $supplier;
					}
				}
				if(!empty($categoryDesc))
				{
					$catTitle = $categoryDesc.$catTitle;
				}
				if(!empty($colorVal))
				{
					if(!empty($supplier) || !empty($categoryDesc)){
						if(!empty($supplier) && empty($categoryDesc)){
							$catTitle = $colorVal.$catTitle;
						}
						else{
							$catTitle = $colorVal." ".$catTitle;
						}
					}
					else{
						$catTitle = $colorVal;				
					}
				}
			}
			ListItemHTML::getListItemHtml($requestARR,$getidCategory,$supplierId,$hasListItemData,$categoryURL,$catTitle);
			$totItem = Functions::getSessionVariable("totItem","");
			$category = Functions::getSessionVariable("category","");
			if(!empty($totItem) && !empty($catTitle))
			{
				if(!empty($supplier)){
					$catTitle = $totItem."+ ".$catTitle." | The Classy Home";
				}
				else{
					$catTitle = $totItem."+ ".$catTitle." by The Classy Home";
				}
			}
			else if(!empty($totItem) && !empty($category))
			{
				$shortByField = Functions::getRequest("shortByField");
				$idCategorySearch = Functions::getRequest("idCategorySearch");
				if($shortByField == "newArrive" && $idCategorySearch == "279"){
					$category = "Latest Collection";
				}
				$catTitle = $totItem."+ ".$category." by The Classy Home";
				unset($_SESSION['totItem']);
				unset($_SESSION['category']);
			}
			else
			{
				if(!empty($listItemTitle))
				{
					$catTitle = $colorName.$listItemTitle;
				}
				else
				{
					if(!empty($pStyle))
					{
						$pStyle = ", Style-".$pStyle;
					}
					else
					{
						$pStyle = "";
					}
					$catTitle = $colorName.$categoryDescription." ".$pCompany." ".$pHeaderKeywords." ".$pStyle;
				}
			}
			?>
			<script>
			var isReplaceTitle = true;
			<?php
			if((isset($_REQUEST["modernFurniture"]) && $_REQUEST["modernFurniture"] == 1) || (isset($_REQUEST["DiscountedFurniture"]) && $_REQUEST["DiscountedFurniture"] == 1) || (isset($_REQUEST["hotDeal"]) && $_REQUEST["hotDeal"] == -1) || (isset($_REQUEST["colorCollection"]) && $_REQUEST["colorCollection"] == -1) || (isSet($_REQUEST["clearanceSale"])) || (isSet($_REQUEST["fastdeliveryfurn"]) && $_REQUEST["fastdeliveryfurn"]=='inStock'))
			{
				?>
				isReplaceTitle = false;
				<?php
			}
			?>
			document.addEventListener('DOMContentLoaded',function(){
				if($(".LandingProduct").length == 0 && isReplaceTitle)
				{
					<?php
					$catTitle = str_replace('"'," inch",$catTitle);
					?>
					document.title="<?php echo $catTitle; ?>" ;
				}
				loadLazyImages();
			});
			</script>
			<?php
		}
		if($categoryURL){
			include_once('cacheend.php'); 
		}
	}?>
	
    </section>
<?php
include_once("footer.php");
?>
</div>

<div class="BackToTop"><i class="fa fa-2x fa-arrow-circle-up"></i></div>

<?php
	include_once("popUpHTML.html");
?>
</body>


<?php
	include_once("footerScript.php");	
?>
<?php
	
	$cssLanding = array();
	array_push($cssLanding, GlobalVariable::$cssPath."listItem.css");
	// array_push($cssLanding, GlobalVariable::$cssPath."landing.css");
	array_push($cssLanding, GlobalVariable::$cssPath."dd.css");
	$cssLandingAndDropDownFile = Functions::createCssORJs("listItem-$device.css",$cssLanding);
	
?>
<noscript id="deferred-styles-listItem"><link rel="stylesheet" type="text/css" href="<?php echo $cssLandingAndDropDownFile;?>" /></noscript>

<script>

var loadDeferredStyles1 = function() {
	var addStylesNode1 = document.getElementById("deferred-styles-listItem");
	var replacement1 = document.createElement("div");
	replacement1.innerHTML = addStylesNode1.textContent;
	document.body.appendChild(replacement1);
	addStylesNode1.parentElement.removeChild(addStylesNode1);
  };
  var raf1 = requestAnimationFrame || mozRequestAnimationFrame ||
	  webkitRequestAnimationFrame || msRequestAnimationFrame;
  if (raf1) raf1(function() { window.setTimeout(loadDeferredStyles1, 0); });
  else window.addEventListener('load', loadDeferredStyles1);
  
  
var bLazy;
function loadLazyImages()
{
	bLazy = new Blazy({
		error: function(ele, msg){
            if(msg === 'missing'){
                $(ele).attr({"src":resizeImageReqUrl+$(ele).attr("data-image")})
            }
            else if(msg === 'invalid'){
                $(ele).attr({"src":resizeImageReqUrl+$(ele).attr("data-image")})
            }  
        }
    });
}
function setExpireCountDown(expireInSecond)
{
	if(expireInSecond >= 86400)
	{
		$(".view-item-deals-end-back").css('width',"220px");
		$('#expireCountDown').timeTo({
			seconds: expireInSecond,
			displayDays: 2
		});
	}
	else
	{
		$('#expireCountDown').timeTo(expireInSecond);
	}
}
$(document).ready(function(){
	<?php if (isSet($googleItem) && $googleItem != '') { ?>
		$('#googleItemMoreDec').show();
		$('#categorypageMoreDec').hide();
	<?php } else { ?>
		$('#categorypageMoreDec').show();
	<?php } ?>
});
</script>

<?php
$JsFiles= array();
array_push($JsFiles, GlobalVariable::$jsPath."jquery.sticky-kit.min.js");
array_push($JsFiles, GlobalVariable::$jsPath."jquery.collapse.js");
// array_push($JsFiles, GlobalVariable::$jsPath."jquery.ui.autocomplete.js");
array_push($JsFiles, GlobalVariable::$jsPath."listItem.js");
array_push($JsFiles, GlobalVariable::$jsPath."jquery.mCustomScrollbar.concat.min.js");
// array_push($JsFiles, GlobalVariable::$jsPath."beLazy.js");
array_push($JsFiles, GlobalVariable::$jsPath."beLazy.js");
array_push($JsFiles, GlobalVariable::$jsPath."jquery.dd.min.js");

$jslistItemCacheFile = Functions::createCssORJs("list-Item.js",$JsFiles);

?>
<script type="text/javascript" src="<?php echo $jslistItemCacheFile;?>" defer="defer"></script>
<?php
if($hideSearch){
?>
<script type="text/javascript" src="<?=GlobalVariable::$jsPath;?>jquery.countdown.js" defer="defer"></script>
<script>
document.addEventListener('DOMContentLoaded',function(){
	if(hideSearch == true)
	{
		setExpireCountDown(<?=$ProductDetails->expireInSecond;?>);
	}
});
</script>
<?php
}
?>
<?php
if(!$categoryURL && !empty($azureResult) && !$azureResult && false)
{
	?>
	<script type="text/javascript">
	$(document).ready(function(){
		if(hasListItemData && false)
		{
			callbacksListItemFirstPart.removeAll();
			var objFilter = null;
			<?php
			$filterFieldArr = GlobalVariable::getFilterFieldArr();			
			if(Functions::getRequest("strSearch") != "")
			{
				$filterArray = GlobalVariable::getCategoryFilterArray("","");
				$filterPosArr = $filterArray['Position'];
			}
			else
			{
				$getidCategory = Functions::getRequest("idCategory");
				if(empty($getidCategory)){
					$getidCategory = Functions::getRequest("idCategorySearch");
				}
				$filterArray = GlobalVariable::getCategoryFilterArray($getidCategory,"");
				$filterPosArr = $filterArray['Position'];
			}
			$temp = array();
			asort($filterPosArr);
			foreach($filterPosArr as $key=>$value)
			{
				$temp[$key] = $filterFieldArr[$key];
			}
			$filterCount = 0;
			?>				
			<?php
			foreach($temp as $key=>$value)
			{
				$filterCount++;
			?>
			objFilter = {filterId:"<?php echo $key; ?>",filterCount:<?php echo $filterCount; ?>,callbackSortedObj:callbacksListItemFirstPart};
			callbacksListItemFirstPart.add(getFilterHtmlOneByOne,<?php echo $filterCount; ?>,objFilter);
			<?php
			}
				$filterCount++;	
			?>
			objFilter = {filterId:"jQueryCollapseFilter",filterCount:<?php echo $filterCount; ?>,callbackSortedObj:callbacksListItemFirstPart};
			callbacksListItemFirstPart.add(jQueryCollapseFilter,<?php echo $filterCount; ?>,objFilter);
			<?php
			$filterCount++;	
			?>
			objFilter = {filterId:"stackifyFilter",filterCount:<?php echo $filterCount; ?>,callbackSortedObj:callbacksListItemFirstPart};
			callbacksListItemFirstPart.add(stackifyFilter,<?php echo $filterCount; ?>,objFilter);
			setTimeout(executeCallbacksNext(callbacksListItemFirstPart),1000);
		}
	});
	</script>
	<?php
}else{
?>
	<script type="text/javascript">
	$(document).ready(function(){
		customScrollBar();
		jQueryCollapseFilter();
		stackifyFilter();
	});
	</script>
	<?php
	if(!empty($azureResult) && $azureResult){
		?>
		<script type="text/javascript">var appInsights=window.appInsights||function(config){function r(config){t[config]=function(){var i=arguments;t.queue.push(function(){t[config].apply(t,i)})}}var t={config:config},u=document,e=window,o="script",s=u.createElement(o),i,f;s.src=config.url||"//az416426.vo.msecnd.net/scripts/a/ai.0.js";u.getElementsByTagName(o)[0].parentNode.appendChild(s);try{t.cookie=u.cookie}catch(h){}for(t.queue=[],i=["Event","Exception","Metric","PageView","Trace","Dependency"];i.length;)r("track"+i.pop());return r("setAuthenticatedUserContext"),r("clearAuthenticatedUserContext"),config.disableExceptionTracking||(i="onerror",r("_"+i),f=e[i],e[i]=function(config,r,u,e,o){var s=f&&f(config,r,u,e,o);return s!==!0&&t["_"+i](config,r,u,e,o),s}),t}
		({
		instrumentationKey: "8755f656-2f9c-4496-b764-06ae493b5c35"
		});
		window.appInsights=appInsights;
		</script>
		<?php
	}
}
$pageType = "category";
if(Functions::getRequest("strSearch") != "")
{
	$pageType = "searchresults";
}
?>
<script>

var prodArr = [];
for(i=0;i<8;i++)
{
	prodArr.push($(".Product.itemList[data-itemno='"+i+"']").attr("data-sku"));
}
if(prodArr.length > 0)
{
	var google_tag_params = {
		ecomm_prodid: prodArr,
		ecomm_pagetype: '<?=$pageType;?>',
		dynx_itemid: prodArr,
		dynx_pagetype: '<?=$pageType;?>',
		<?php
		if(!empty($categoryDescription)){
		?>
		ecomm_category: '<?=$categoryDescription;?>',
		<?php
		}
		?>
	};
}
$(document).ready(function(){
	callbackImpression();
	addProductClickGA();
});

</script>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId            : '314550968926987',
      version          : 'v3.1'
    });
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "https://connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>
</html>