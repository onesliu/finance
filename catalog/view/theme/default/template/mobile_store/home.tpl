<?php echo $header; ?>
<body>
<style type="text/css">
@media ( min-width: 20em ) {
	.category-box {
		width: 100%;
		padding: 0;
		text-align: center;
		vertical-align:middle;
		margin:0 auto;
	}
	
	.category-box a {
		text-decoration:none;
	}
	.category-box a:hover {
	}
	
	.category-box .boxes {
		float: left;
		width: 32%;
		margin: .125em;
		padding: 0;
		border:1px solid none;
	    -webkit-border-radius: 4px;
	    border-radius: 4px;
	}
	
	.category-box .big-box {
		height: 8em;
	}
	
	.category-box .small-box {
		height: 4em;
	}
	
	.category-box .green-box {
		background: #00dd99;
	}
	
	.category-box .orange-box {
		background: #FFCC33;
	}
	
	.category-box .purple-box {
		background: #EEAAEE;
	}
	
	.category-box .boxes h2 {
		width: 100%;
		margin:0 auto;
	    color: #fff;
		font-weight:normal;
		text-shadow : 0 0 0;
	}
	
	.category-box .big-box h2 {
		font-size: 1.2em;
		height: 1em;
		margin: .8em 0;
	}
	
	.category-box .small-box h2 {
		font-size: .8em;
		height: 1em;
		margin: .5em 0;
	}
	
	.category-box .big-box .icon {
		width: 50%;
		height: 4em;
		margin:0 auto;
	}
	
	.category-box .small-box .icon {
		width: 40%;
		height: 1.8em;
		margin:0 auto;
	}
}
</style>
<div data-role="page" data-theme="a" id="homepage">
	<?php echo $titlebar; ?>
	<div data-role="content">
		<?php if (isset($actions) && count($actions) > 0) { ?>
		<div id="homeswipe" class="swiper-container">
			<div class="swiper-wrapper">
				<?php foreach($actions as $act) {?>
				<div class="swiper-slide">
					<a href="<?php echo $act->href; ?>"><img src="<?php echo $act->image; ?>" /></a>
				</div>
				<?php } ?>
			</div>
			<div class="swiper-pagination swiper-pagination-white"></div>
		</div>
		<?php } ?>
		
		<div class="category-box">
	  		<a href="<?php if (isset($category['蔬菜'])) echo $category['蔬菜']['href']; else echo '#'; ?>">
			  	<div class="boxes big-box green-box">
			  		<h2><?php echo '蔬 菜'; ?></h2>
			  		<div class="icon ui-icon-chilis"></div>
			  	</div>
			</a>
	  		<a href="<?php if (isset($category['省心菜'])) echo $category['省心菜']['href']; else echo '#';  ?>">
			  	<div class="boxes big-box green-box">
			  		<h2><?php echo '省心菜'; ?></h2>
			  		<div class="icon ui-icon-soup3"></div>
			  	</div>
			</a>
	  		<a href="<?php if (isset($category['副食品'])) echo $category['副食品']['href']; else echo '#';  ?>">
			  	<div class="boxes big-box orange-box">
			  		<h2><?php echo '副食品'; ?></h2>
			  		<div class="icon ui-icon-salty1"></div>
			  	</div>
			</a>
	  		<a href="<?php if (isset($category['肉类'])) echo $category['肉类']['href']; else echo '#';  ?>">
			  	<div class="boxes big-box purple-box">
			  		<h2><?php echo '肉 类'; ?></h2>
			  		<div class="icon ui-icon-chicken7"></div>
			  	</div>
			</a>
	  		<a href="<?php if (isset($category['水果'])) echo $category['水果']['href']; else echo '#';  ?>">
			  	<div class="boxes big-box purple-box">
			  		<h2><?php echo '水 果'; ?></h2>
			  		<div class="icon ui-icon-apple12"></div>
			  	</div>
			</a>
	  		<a href="#category_panel">
			  	<div class="boxes big-box orange-box">
			  		<h2><?php echo '更 多'; ?></h2>
			  		<div class="icon ui-icon-more"></div>
			  	</div>
			</a>
	  	</div>
	</div>
	<?php echo $navi; ?>
	<script type="text/javascript"><!--
	$(document).on("pageinit", "#homepage", function(){
		if (typeof homeswipe != "undefined") {
			setTimeout(function(){
				var mySwiper = new Swiper('.swiper-container',{
				    pagination: '.swiper-pagination',
				    paginationClickable: true,
				    autoplay: 2000,
				    loop: true
				  });
				mySwiper.update();
			}, 500);
		}
	});
	//--></script>
</div>
</body>
