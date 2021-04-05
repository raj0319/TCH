<?php
include_once("include/constant.php");

echo "test";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php 
	$pHomeTitle = Functions::getSettingKey("pHomeTitle");
	$pHomeMetaDescription = Functions::getSettingKey("pHomeMetaDescription");
	
	$pHomeKeywords = Functions::getSettingKey("pHomeKeywords");	
	include_once($_SERVER["DOCUMENT_ROOT"]."/defaultTitle.php");
	
	include_once("headTag.php"); ?>
	<meta name="application-name" content="The Classy Home"/>
	
	<link rel="apple-touch-icon" href="/favicon-32x32.png">
	<link rel="apple-touch-startup-image" href="https://cdn.theclassyhome.com/newimages/site/logo.gif">
	<meta name="apple-mobile-web-app-title" content="The Classy Home">
	
	<meta name="twitter:card" content="summary_large_image" />
	<meta name="twitter:title" content="<?php echo $pHomeTitle; ?>" />
	<meta name="twitter:description" content="<?php echo $pHomeMetaDescription; ?>" />
	
	<meta property="og:url" content="<?php echo domainUrlHTTPS; ?>">
	<meta property="og:type" content="website" />
	<meta property="og:title" content="<?php echo $pHomeTitle; ?>" />
	<meta property="og:description" content="<?php echo $pHomeMetaDescription; ?>" />
	<meta property="og:image" content="https://cdn.theclassyhome.com/newimages/site/logo.gif" />
	
	<?php
		$cssMinFileHeader = array();
		array_push($cssMinFileHeader, GlobalVariable::$cssPath."header.css");
		$cssFileHeader = Functions::createCssORJs("header-desktop.css",$cssMinFileHeader);
	?>
	<link rel='stylesheet' type='text/css' href='<?=$cssFileHeader;?>' />
	<style>
		<?php // include_once($_SERVER["DOCUMENT_ROOT"]."/headerStyle.php"); ?>
		/** MAIN BANNER START **/
		.MainBanner{text-align:center;}
		.MainBanner .BannerTab{border-bottom:1px solid #f4f4f4; padding:15px 0; text-align:center; height:70px; background: #fff}
		.MainBanner .BannerTab a{width:20%; float:left; text-align:center; font-size:16px; font-weight:500; text-transform:uppercase; color:#444; line-height:22px; border-right:1px solid #f4f4f4; padding:12px 0px; position:relative; margin-left:-1px; cursor:pointer;}
		.MainBanner .BannerTab a:last-child{border:0;}
		.MainBanner .BannerTab a span{font-size:12px; font-weight:normal;}
		.MainBanner .BannerTab a.active:after{content:""; height:1px; width:100%; background:#d52b2a; position:absolute; left:0; bottom:-18px;}
		.MainBanner .BannerTab a.active{color:#d52b2a;}
		.MainBanner .BannerTab a.active span{color:#444;}
		.MainBanner .HomeBanner .item{text-align:center;}
		.HomeBanner .item .lazy.b-loaded{width:auto !important;}
		.HomeBanner  .item img{width:auto !important; }
		.MainBanner .owl-carousel .owl-item img{display:inline-block;}
		
		@media screen and (min-width: 0px) and (max-width: 1080px) {

			.MainBanner .owl-carousel .owl-item video{width:1080px;}
		}		
		@media screen and (min-width: 1081px) and (max-width: 1300px) {

			.MainBanner .owl-carousel .owl-item video{width:1300px;}
		}
		@media screen and (min-width: 1301px) and (max-width: 1600px) {

			.MainBanner .owl-carousel .owl-item video{width:1583px;}
		}
		
		/** index Middle **/
		h2.StyleTitle span{font-size:14px; font-family: 'Roboto', sans-serif;}
		h2.StyleTitle:after{content:""; height:1px; width:100px; margin-left:-50px; background:#d52b2a; position:absolute; left:50%; bottom:-10px;}	
		h1.StyleTitle2{font-family: 'Oleo Script', cursive; font-size: 30px; text-align: center; font-weight: normal; position: relative; margin: 0 0 24px 0;}
		h1.StyleTitle2:after{display:none;}
		.StyleTitle2{font-size: 14px; font-family: 'Roboto', sans-serif; display: block; text-align: center; height: 20px;  position: relative; margin-bottom: 30px;}
		.StyleTitle2:after{content:""; height:1px; width:100px; margin-left:-50px; background:#d52b2a; position:absolute; left:50%; bottom:-10px;}

		/** About TCH **/
		.AboutTCH{padding-top:40px;}
		.AboutTCH h1{font-family: 'Oleo Script', cursive; font-size:34px; text-align:center; font-weight:normal; position:relative; margin:0 0 40px 0;}
		.AboutTCH h1:after{content:""; height:1px; width:100px; margin-left:-50px; background:#d52b2a; position:absolute; left:50%; bottom:-5px;}
		.AboutTCH .TCHVideo{border:1px solid #e4e4e4; padding:10px; width:100%; max-width:42%; float:left;}
		.AboutTCH .TCHVideo .VideoContainer{position:relative; padding-bottom:56.25%; /* padding-top:35px; */ height:0; overflow:hidden;}
		.AboutTCH .TCHVideo .VideoContainer iframe{position:absolute; top:0; left:0; width:100%; height:100%;}
		.AboutTCH .TCHContent{padding-left:30px; margin-left:44%;}
		.AboutTCH .TCHContent h3{font-size:16px;}
		.AboutTCH .TCHContent a.RRBTN{margin:30px auto 0 auto; display:block; width:80px; text-align:center;}
		.discountedFurniture{ text-align:center; padding-top: 30px; }
	</style>
	<?php
	$cssMinFile = array();
	array_push($cssMinFile, GlobalVariable::$cssPath."index.css");
	$cssFile = Functions::createCssORJs("index-$device.css",$cssMinFile);
	?>
	<link rel='stylesheet' type='text/css' href='<?=$cssFile;?>'>
	
</head>
<body>	
	<?php
	 include_once("header.php");
	?>	
	<div class="stickyBodyContent">
	
	<?php 
		include_once($_SERVER["DOCUMENT_ROOT"]."/holidayHeader.php");	
		$cdnImageArray = GlobalVariable::getCDNImagePath();
		$imagePathSite = $cdnImageArray["CDNRootPath"].GlobalVariable::$azureImageFolder."site/";
	?>
	  <section class="MainBanner">
			<?php
			// if($isVideoAddedInSaleBanner != true)
			// {
				?>
				<div class="HomeBanner1" style="display:block !important;">
					<div class="item" ><img src="<?php echo $saleBannerImagePath.$folderName;?>slider-banner-1.jpg" data-index="0" alt="<?=$currentSaleName;?>"/></div>
				</div>
				<?php
			// }
			?>
		  <div class="owl-carousel HomeBanner" style="display:none;">
			
			<?php			
			if($isVideoAddedInSaleBanner == true)
			{
				?>
				<div class="item" data-videosrc="<?php echo $saleBannerImagePath.$folderName;?>slider-banner-1.mp4" ><img src="" data-index="0" /></div>
				<?php
			}else 
			{
			?>
				<div class="item" ><a href="/ListProduct/DiscountedFurniture" ><img src="<?php echo $saleBannerImagePath.$folderName;?>slider-banner-1.jpg" data-index="0" alt="<?=$currentSaleName;?>"/></a></div>			
			<?php
			} ?>
			<!--div class="item" ><a href="/category/Mattresses/226"><img class="lazy" src="<?//=$imagePathSite;?>dot.gif" data-src="<?php //echo $imagePathSite;?>main-banner2.jpg" data-index="1" alt="100% Money Back On Any Mattress"/></a></div-->
			<div class="item" ><a href="/category/FastDeliveryFurniture"><img class="lazy" src="<?=$imagePathSite;?>dot.gif" data-src="<?php echo $imagePathSite;?>main-banner2.jpg" data-index="1" alt="FastDelivery Furniture"/></a></div>
			<div class="item" ><a href="/ListProduct/clearanceSale"><img  class="lazy" src="<?=$imagePathSite;?>dot.gif" data-src="<?php echo $imagePathSite;?>main-banner3.jpg" data-index="2" alt="Clearance Sale"/></a></div>
			<div class="item" ><a href="/ListProduct/modernFurniture"><img class="lazy" src="<?=$imagePathSite;?>dot.gif" data-src="<?php echo $imagePathSite;?>main-banner4.jpg" data-index="3" alt="Modern Furniture"/></a></div>
			<div class="item" ><a href="/AllSuppliers.php"><img class="lazy" src="<?=$imagePathSite;?>dot.gif" data-src="<?php echo $imagePathSite;?>main-banner5.jpg" data-index="4" alt="BRANDS YOU TRUST"/></a></div>
		  </div>
		<div class="SiteContainer">
		  <div class="BannerTab"> 
		  <a data-index="0" class="headerSliderTab active" id="0"><?=$currentSaleName;?><br />
			<span><?=$saleSortDescription;?></span></a> 
		  <a data-index="1" class="headerSliderTab" id="1" >Furniture Ready to ship<br />
			<span>In Stock | Ready To Ship</span></a> 
		  <a data-index="2" class="headerSliderTab" id="2" >Clearance Sale<br />
			<span>Up To 40% OFF</span></a> 
		  <a data-index="3" class="headerSliderTab" id="3" >Modern Furniture<br />
			<span>Exclusive Range</span></a> 
		  <a data-index="4" class="headerSliderTab" id="4" >BRANDS YOU TRUST<br />
			<span>Great Selection</span></a></div>
		</div>
	  </section>
		<?php 
			if($isSupplierProductDiscount)
			{
				$curBanner = "discounted-furniture.jpg";
				?>
				<div class="discountedFurniture">
					<a href="/ListProduct/DiscountedFurniture" alt="Discounted Furniture"><img class="lazy" src="<?=$imagePathSite;?>dot.gif" data-src="<?php echo $imagePathSite.$curBanner;?>" alt="Discounted Furniture"/></a>
				</div>
				
				<h2 id='specialDealBrandHeader' class="StyleTitle" style='margin: 20px 0 20px 0 !important;display:none;'>Price Drop From Brands</h2>
				<section style='background:#fff;' class="specialDealBrandLogo">
				</section>
				
	  <?php } ?>
	  
	  <!--
	  <section class="OfferBanner" style='background:#f2d635;margin-top:40px;'>
		<div class="SiteContainer">
		  <div class="BannerBlock"><a href="/ListProduct/latestCollection"><img class="lazy" src="<?=$imagePathSite;?>loading-dots.gif" data-src="<?php echo $imagePathSite ?>offer-banner1-b.jpg" alt="New Design Collection"/></a></div>
		  <div class="BannerBlock"><a href="/ListProduct/colorCollection"><img class="lazy" src="<?=$imagePathSite;?>loading-dots.gif" data-src="<?php echo $imagePathSite ?>offer-banner2-b.jpg" alt="Color Collection"/></a></div>
		  <div class="BannerBlock"><a href="/ListProduct/modernFurniture"><img class="lazy" src="<?=$imagePathSite;?>loading-dots.gif" data-src="<?php echo $imagePathSite ?>offer-banner3-b.jpg" alt="Modern Furniture"/></a></div>
		  <div class="BannerBlock last"><a href="<?php echo Functions::createListOneCateURL('316','Decor');?>"><img class="lazy" src="<?=$imagePathSite;?>loading-dots.gif" data-src="<?php echo $imagePathSite ?>offer-banner4-b.jpg" alt="Decor Your Home"/></a></div>
		  <div class="clear"></div>
		</div>
	  </section>-->
	  
	  <?php echo IndexHTML::indexHomeCategoryListHTML() ?>
		<section class="TrendingProducts">
		<div class="SiteContainer">
		  <h2 class="StyleTitle">Trending Products<br />
			<span>People Love it</span></h2>
		  	<div class="ProductRow" id="SeeWhatsTrendingTab">
			</div>
		</div>
		</section>
		<section class="BestSellersProducts">
			<div class="SiteContainer">
				<h2 class="StyleTitle">Shop Bestsellers<br />
				<span>Top Selling item</span></h2>
				<div class="ProductRow" id="topSellingItems">
					<?php echo IndexHTML::getshopBestSellerItems(); ?>
				</div>
				
			</div>
		</section>
	  <?php echo IndexHTML::indexSubCategoryList(); ?>
	  <?php // echo IndexHTML::indexHomeShopByLookHTML(); ?>
	
	  <section class="OurCustomersSay">
		  <div class="SiteContainer">
			  <?php echo ViewItemHTML::getReviewsWithPhotoHTML();?>
		  </div>
	  </section>
	  
	  <section class="OfferBanner" >
		<div class="SiteContainer">
		  <div class="BannerBlock"><a href="/ListProduct/latestCollection"><img class="lazy" src="<?=$imagePathSite;?>loading-dots.gif" data-src="<?php echo $imagePathSite ?>offer-banner1-b.jpg" alt="New Design Collection"/></a></div>
		  <div class="BannerBlock"><a href="/ListProduct/colorCollection"><img class="lazy" src="<?=$imagePathSite;?>loading-dots.gif" data-src="<?php echo $imagePathSite ?>offer-banner2-b.jpg" alt="Color Collection"/></a></div>
		  <div class="BannerBlock"><a href="/ListProduct/modernFurniture"><img class="lazy" src="<?=$imagePathSite;?>loading-dots.gif" data-src="<?php echo $imagePathSite ?>offer-banner3-b.jpg" alt="Modern Furniture"/></a></div>
		  <div class="BannerBlock last"><a href="<?php echo Functions::createListOneCateURL('316','Decor');?>"><img class="lazy" src="<?=$imagePathSite;?>loading-dots.gif" data-src="<?php echo $imagePathSite ?>offer-banner4-b.jpg" alt="Decor Your Home"/></a></div>
		  <div class="clear"></div>
		</div>
	  </section>
	 
	  <section class="BrandLogo">
	  </section>
	<?php //echo IndexHTML::getIndexSupplierHtml();?>
	<?php include_once("footer.php");?>

	</div>
	<div class="BackToTop"><i class="fa fa-2x fa-arrow-circle-up"></i></div>

<!-- 	<script src="js/jquery.min.js"></script>  -->
<!-- 	<script src="js/owl.carousel.min.js"></script>  -->
<!-- 	<script src="js/owl.hash.js"></script>  -->
	<!-- Rocket Scroll --> 
<!-- 	<script src="js/rocketHelpers.js"></script>  -->
<!-- 	<script src="js/rocketScroll.min.js"></script>  -->
<!-- 	<script src="js/masonry.pkgd.min.js"></script>  -->
<!-- 	<script src="javascript/functionsCallWithPriority.js"></script> -->
<!-- 	<script src="js/index.js"></script>  -->
<!-- 	<script src="js/global.js"></script> -->
	
<?php
	include_once("footerScript.php");
	$JsFiles= array();
	array_push($JsFiles, GlobalVariable::$jsPath."masonry.pkgd.min.js");
	array_push($JsFiles, GlobalVariable::$jsPath."index.js");
	array_push($JsFiles, GlobalVariable::$jsPath."jquery.lazy.js");
    $jsIndexCacheFile = Functions::createCssORJs("index.js",$JsFiles);
?>
<script type="text/javascript" src="<?php echo $jsIndexCacheFile;?>" defer="defer"></script>
<script type="text/javascript" >
	$(document).ready(function() {
		if($('.textReview .item').length > 0)
		{
			$("#customerReviewSlider").show();		
		}	
	});
	var google_tag_params = {
		ecomm_pagetype: 'home',
		dynx_pagetype: 'home',
	};
</script>
</body>
</html>

