@extends('beautician.main-layout.main-layout')
@section('title') {{'Fixhibition'}}@stop
@section('content')
        <div class="inner-container fix-pg">
            <div class="tab-section main-page-tabs">
                <div class="common-tabs">
                    <ul class="tabs">
                        <li class="tab col s3"><a class="active all-work"  href="#allwork">All Work</a></li>
                        <li class="tab col s3"><a href="#mywork" class="my-work">My Work</a></li>
                    </ul>
                </div>
                <div class="upload-icon fix-img-upload">
                    <input class="profile-img" id="profile-img2" type="file" class="file" name="fixhibition-image" />
                </div>
            </div>

            <div class="captions-wrap">
                <div id="allwork">
                    <div class="row fix-img">
                        <div id="lightgallery" class="lightgallery">
                        </div></div>
                </div>
                <div id="mywork">
                    <div class="row fix-img">
                        <div id="lightgallery2" class="lightgallery2">
                        </div></div>
                </div>
            </div>
        </div>
<div class="load-more" style="display:none">Load more</div>
@stop
@section('scriptjs')
<script>
/* LIGHTGALLERY AUTOUPDATE */
!function(e,t,i,s){"use strict";var o={autoUpdate_itemsAutoUpdate:!0,autoUpdate_imageCount:0,autoUpdate_hasRefreshEvent:!1},r=function(e){return this.core=jQuery(e).data("lightGallery"),this.$el=jQuery(e),this.core.s=jQuery.extend({},o,this.core.s),this.init(),this};r.prototype.init=function(){if(this.core.s.autoUpdate_itemsAutoUpdate){if(0!==this.core.s.autoUpdate_imageCount){var e=this;this.$el.on("onAfterOpen.lg",function(){e.setCounter(e.core.s.autoUpdate_imageCount)})}this.core.s.autoUpdate_hasRefreshEvent||(this.$el.on("refreshItems",this.refreshItems),this.core.s.autoUpdate_hasRefreshEvent=!0)}},r.prototype.refreshItems=function(){var e=this;this.core=jQuery(this).data("lightGallery"),this.core.s.dynamic?this.core.$items=this.core.s.dynamicEl:"this"===this.core.s.selector?this.core.$items=this.core.$el:""!==this.core.s.selector?this.core.s.selectWithin?this.core.$items=jQuery(this.core.s.selectWithin).find(this.core.s.selector):this.core.$items=this.core.$el.find(jQuery(this.core.s.selector)):this.core.$items=this.core.$el.children();for(var t=this.core.$items.length-this.core.$outer.find(".lg-inner").find(".lg-item").length;t>0;){var i=jQuery('<div class="lg-item"></div>');this.core.$outer.find(".lg-inner").append(i),this.core.$slide=this.core.$outer.find(".lg-item"),t--}this.core.$items.on("click.lgcustom",function(t){try{t.preventDefault()}catch(e){t.returnValue=!1}e.core.$el.trigger("onBeforeOpen.lg"),e.core.index=e.core.s.index||e.core.$items.index(this),jQuery("body").hasClass("lg-on")||(e.core.build(e.core.index),jQuery("body").addClass("lg-on"))}),this.core.enableSwipe(),this.core.enableDrag(),0===this.core.s.autoUpdate_imageCount&&this.core.modules.autoUpdate.setCounter(this.core.$items.length)},r.prototype.setCounter=function(e){this.core.$outer.find("#lg-counter-all").html(e)},r.prototype.destroy=function(){},jQuery.fn.lightGallery.modules.autoUpdate=r}(jQuery,window,document);
</script>
<!--<script type="text/javascript" src="{{URL::asset('assets/beautician/js/lightgallery.js')}}"></script>-->
<script type="text/javascript" src="{{URL::asset('assets/beautician/js/fixhibition.js')}}"></script>
@stop