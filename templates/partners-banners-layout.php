<?php get_header(); ?>

<script>
jQuery(document).ready(function($) {
$('.pagepartnerdescription').addClass('hidden');
$('.pagepartner').hover(function () {
    $(this).find('.pagepartnerdescription').removeClass('hidden').addClass('hover');
}, function () {
    $(this).find('.pagepartnerdescription').removeClass('hover').addClass('hidden');
});
});
</script>
<style type="text/css">
/* PARTNERS PAGE */
.pageallpartners {
width:80%;
margin-left:18%;
background-color: white;
   /* Prevent vertical gaps */
   line-height: 0;
   
   -webkit-column-count: 5;
   -webkit-column-gap:   0px;
   -moz-column-count:    5;
   -moz-column-gap:      0px;
   column-count:         5;
   column-gap:           0px;
}

.pageallpartners .pagepartner .pagepartnerimage a img {
  /* Just in case there are inline attributes */
  width: 100% !important;
  height: auto !important;
}

.pageallpartners .pagepartner .pagepartnerimage a img:hover{
opacity: 0.5;
}

@media (max-width: 1200px) {
  .pageallpartners {
  -moz-column-count:    4;
  -webkit-column-count: 4;
  column-count:         4;
  }
}
@media (max-width: 1000px) {
  .pageallpartners {
  width:100%;
  margin-left:0;
  -moz-column-count:    3;
  -webkit-column-count: 3;
  column-count:         3;
  }
}
@media (max-width: 800px) {
  .pageallpartners {
  -moz-column-count:    2;
  -webkit-column-count: 2;
  column-count:         2;
  }
  
  .sub_header_wrapper{
  padding-left: 0 !important;
  }
  #mid_container {
    padding-left: 0 !important;
}
#mid_container_wrapper {
    padding-left: 0 !important;
}
}
@media (max-width: 400px) {
  .pageallpartners {
  -moz-column-count:    1;
  -webkit-column-count: 1;
  column-count:         1;
  }
}

.hidden{
display:none;
}

.hover{
position: fixed;
display: inherit;
bottom: 0;
background-color: black;
width: 100%;
text-align: center;
left: 0;
z-index: 9999;
}
/* #PARTNERS PAGE */
</style>
<div class="pageallpartners">

<?php 

$args = array( 'post_type' => 'partnersbanner', 'posts_per_page' => '-1');
$loop = new WP_Query( $args );

$counter = 1;
     if( $loop->have_posts() ):
				
		while( $loop->have_posts() ): $loop->the_post(); global $post;              
?>
    <div class="pagepartner">
    <div class="pagepartnerimage">
    <a href="<?php echo $partnerlink=get_post_meta(get_the_id(),'partnerlink',true);?>" target="_blank">
    <?php the_post_thumbnail( 'full' );?>
    </a> 
     </div>    
    <div class="pagepartnerdescription">
    <?php the_content(); ?>
    </div>
    </div>  
   <?php $counter++ ;?> 
   
   
   
    <?php endwhile; else: ?>
      <p><?php _e('Sorry, there are no partners.'); ?></p>
    <?php endif; ?>

</div>



<?php get_footer(); ?>