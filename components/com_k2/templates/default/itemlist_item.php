<?php
/**
 * @version		3.0.0
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die ;
 ?>

<div>
	<!-- K2 Plugins: K2BeforeDisplay -->
	<?php echo $this->item->events->K2BeforeDisplay; ?>

	<div class="itemHeader">

		<!-- Date created -->
		<span class="itemDateCreated">
			<?php echo JHtml::_('date', $this->item->created, JText::_('K2_DATE_FORMAT_LC2')); ?>
		</span>

	  <!-- Item title -->
	  <h2 class="itemTitle">
			<?php if($this->item->canEdit): ?>
			<!-- Item edit link -->
			<span class="itemEditLink">
				<a href="<?php echo $this->item->editLink; ?>">
					<?php echo JText::_('K2_EDIT_ITEM'); ?>
				</a>
			</span>
			<?php endif; ?>

	  	<?php echo $this->item->title; ?>

	  	<?php if($this->item->featured): ?>
	  	<!-- Featured flag -->
	  	<span>
		  	<sup>
		  		<?php echo JText::_('K2_FEATURED'); ?>
		  	</sup>
	  	</span>
	  	<?php endif; ?>

	  </h2>

		<!-- Item Author -->
		<span class="itemAuthor">
			<?php echo K2HelperUtilities::writtenBy($this->item->author->gender); ?>&nbsp;<?php if(empty($this->item->created_by_alias)): ?>
			<a rel="author" href="<?php echo $this->item->author->link; ?>"><?php echo $this->item->author->name; ?></a>
			<?php else: ?>
			<?php echo $this->item->author->name; ?>
			<?php endif; ?>
		</span>

  </div>

  <!-- Plugins: AfterDisplayTitle -->
  <?php echo $this->item->events->AfterDisplayTitle; ?>

  <!-- K2 Plugins: K2AfterDisplayTitle -->
  <?php echo $this->item->events->K2AfterDisplayTitle; ?>


  <div class="itemBody">

	  <!-- Plugins: BeforeDisplayContent -->
	  <?php echo $this->item->events->BeforeDisplayContent; ?>

	  <!-- K2 Plugins: K2BeforeDisplayContent -->
	  <?php echo $this->item->events->K2BeforeDisplayContent; ?>

	  <?php if($this->item->image): ?>
	  <!-- Item Image -->
	  <div class="itemImageBlock">
		  <span class="itemImage">
		  	<a href="<?php echo $this->item->images['XL']->src; ?>" title="<?php echo JText::_('K2_CLICK_TO_PREVIEW_IMAGE'); ?>">
		  		<img src="<?php echo $this->item->image->src; ?>" alt="<?php echo $this->item->image->alt; ?>" style="width:<?php echo $this->item->imageWidth; ?>px; height:auto;" />
		  	</a>
		  </span>

		  <?php if($this->item->image->caption): ?>
		  <!-- Image caption -->
		  <span class="itemImageCaption"><?php echo $this->item->image->caption; ?></span>
		  <?php endif; ?>

		  <?php if($this->item->image->credits): ?>
		  <!-- Image credits -->
		  <span class="itemImageCredits"><?php echo $this->item->image->credits; ?></span>
		  <?php endif; ?>

		  <div class="clr"></div>
	  </div>
	  <?php endif; ?>

	  <?php if($this->item->fulltext): ?>
	  <!-- Item introtext -->
	  <div class="itemIntroText">
	  	<?php echo $this->item->introtext; ?>
	  </div>
	  <?php endif; ?>

		
	  <?php if($this->item->fulltext): ?>
	  <!-- Item fulltext -->
	  <div class="itemFullText">
	  	<?php echo $this->item->fulltext; ?>
	  </div>
	  <?php endif; ?>

	<div class="clr"></div>

	  <?php if(count($this->item->extraFields)): ?>
	  <!-- Item extra fields -->
	  <div class="itemExtraFields">
	  	<h3><?php echo JText::_('K2_ADDITIONAL_INFO'); ?></h3>
	  	<?php foreach ($this->item->extraFields as $extraFieldGroup): ?>
	  	<h4><?php echo $extraFieldGroup->name; ?></h4>
	  	<ul>
			<?php foreach ($extraFieldGroup->fields as $key=>$extraField): ?>
			<li class="<?php echo ($key%2) ? "odd" : "even"; ?> type<?php echo ucfirst($extraField->type); ?> group<?php echo $extraField->group; ?>">
				<span class="itemExtraFieldsLabel"><?php echo $extraField->name; ?>:</span>
				<span class="itemExtraFieldsValue"><?php echo $extraField->output; ?></span>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php endforeach; ?>
	    <div class="clr"></div>
	  </div>
	  <?php endif; ?>

		<div class="itemContentFooter">

			<!-- Item Hits -->
			<span class="itemHits">
				<?php echo JText::_('K2_READ'); ?> <b><?php echo $this->item->hits; ?></b> <?php echo JText::_('K2_TIMES'); ?>
			</span>

			<!-- Item date modified -->
			<span class="itemDateModified">
				<?php echo JText::_('K2_LAST_MODIFIED_ON'); ?> <?php echo JHTML::_('date', $this->item->modified, JText::_('K2_DATE_FORMAT_LC2')); ?>
			</span>

			<div class="clr"></div>
		</div>

	  <!-- Plugins: AfterDisplayContent -->
	  <?php echo $this->item->events->AfterDisplayContent; ?>

	  <!-- K2 Plugins: K2AfterDisplayContent -->
	  <?php echo $this->item->events->K2AfterDisplayContent; ?>

	  <div class="clr"></div>
  </div>


  <div class="itemLinks">

		<!-- Item category -->
		<div class="itemCategory">
			<span><?php echo JText::_('K2_PUBLISHED_IN'); ?></span>
			<a href="<?php echo $this->item->category->link; ?>"><?php echo $this->item->category->title; ?></a>
		</div>

	  <?php if(count($this->item->tags)): ?>
	  <!-- Item tags -->
	  <div class="itemTagsBlock">
		  <span><?php echo JText::_('K2_TAGGED_UNDER'); ?></span>
		  <ul class="itemTags">
		    <?php foreach ($this->item->tags as $tag): ?>
		    <li><a href="<?php echo $tag->link; ?>"><?php echo $tag->name; ?></a></li>
		    <?php endforeach; ?>
		  </ul>
		  <div class="clr"></div>
	  </div>
	  <?php endif; ?>

	  <?php if(count($this->item->attachments)): ?>
	  <!-- Item attachments -->
	  <div class="itemAttachmentsBlock">
		  <span><?php echo JText::_('K2_DOWNLOAD_ATTACHMENTS'); ?></span>
		  <ul class="itemAttachments">
		    <?php foreach ($this->item->attachments as $attachment): ?>
		    <li>
			    <a title="<?php echo $this->escape($attachment->title); ?>" href="<?php echo $attachment->link; ?>"><?php echo $attachment->name; ?></a>
			    <?php if($this->params->get('itemAttachmentsCounter')): ?>
			    <span>(<?php echo $attachment->downloads; ?> <?php echo ($attachment->downloads==1) ? JText::_('K2_DOWNLOAD') : JText::_('K2_DOWNLOADS'); ?>)</span>
			    <?php endif; ?>
		    </li>
		    <?php endforeach; ?>
		  </ul>
	  </div>
	  <?php endif; ?>

		<div class="clr"></div>
  </div>

  <?php if(!$this->item->created_by_alias): ?>
  <!-- Author Block -->
  <div class="itemAuthorBlock">

  	<?php if($this->item->author->image): ?>
  	<img class="itemAuthorAvatar" src="<?php echo $this->item->author->image->src; ?>" alt="<?php echo $this->escape($this->item->author->name); ?>" />
  	<?php endif; ?>

    <div class="itemAuthorDetails">
      <h3 class="itemAuthorName">
      	<a rel="author" href="<?php echo $this->item->author->link; ?>"><?php echo $this->item->author->name; ?></a>
      </h3>

      <?php if($this->item->author->description): ?>
      <p><?php echo $this->item->author->description; ?></p>
      <?php endif; ?>

      <?php if($this->item->author->site): ?>
      <span class="itemAuthorUrl"><?php echo JText::_('K2_WEBSITE'); ?> <a rel="me" href="<?php echo $this->item->author->site; ?>" target="_blank"><?php echo str_replace('http://', '', $this->item->author->site); ?></a></span>
      <?php endif; ?>

      <span class="itemAuthorEmail"><?php echo JText::_('K2_EMAIL'); ?> <?php echo JHTML::_('Email.cloak', $this->item->author->email); ?></span>
      <?php endif; ?>

			<div class="clr"></div>

			<!-- K2 Plugins: K2UserDisplay -->
			<?php echo $this->item->events->K2UserDisplay; ?>

    </div>
    <div class="clr"></div>
  </div>

  <?php if(!$this->item->created_by_alias && isset($this->authorLatestItems)): ?>
  <!-- Latest items from author -->
	<div class="itemAuthorLatest">
		<h3><?php echo JText::_('K2_LATEST_FROM'); ?> <?php echo $this->item->author->name; ?></h3>
		<ul>
			<?php foreach($this->authorLatestItems as $key=>$item): ?>
			<li class="<?php echo ($key%2) ? "odd" : "even"; ?>">
				<a href="<?php echo $item->link ?>"><?php echo $item->title; ?></a>
			</li>
			<?php endforeach; ?>
		</ul>
		<div class="clr"></div>
	</div>
	<?php endif; ?>

	<?php
	/*
	 Note regarding 'Related Items'!
	 If you add:
	 - the CSS rule 'overflow-x:scroll;' in the element div.itemRelated {…} in the k2.css
	 - the class 'k2Scroller' to the ul element below
	 - the classes 'k2ScrollerElement' and 'k2EqualHeights' to the li element inside the foreach loop below
	 - the style attribute 'style="width:<?php echo $item->imageWidth; ?>px;"' to the li element inside the foreach loop below
	 ...then your Related Items will be transformed into a vertical-scrolling block, inside which, all items have the same height (equal column heights). This can be very useful if you want to show your related articles or products with title/author/category/image etc., which would take a significant amount of space in the classic list-style display.
	 */
	?>

  <?php if($this->params->get('itemRelated') && isset($this->relatedItems)): ?>
  <!-- Related items by tag -->
	<div class="itemRelated">
		<h3><?php echo JText::_("K2_RELATED_ITEMS_BY_TAG"); ?></h3>
		<ul>
			<?php foreach($this->relatedItems as $key=>$item): ?>
			<li class="<?php echo ($key%2) ? "odd" : "even"; ?>">

				<?php if($this->params->get('itemRelatedTitle', 1)): ?>
				<a class="itemRelTitle" href="<?php echo $item->link ?>"><?php echo $item->title; ?></a>
				<?php endif; ?>

				<?php if($this->params->get('itemRelatedCategory')): ?>
				<div class="itemRelCat"><?php echo JText::_("K2_IN"); ?> <a href="<?php echo $item->category->link ?>"><?php echo $item->category->name; ?></a></div>
				<?php endif; ?>

				<?php if($this->params->get('itemRelatedAuthor')): ?>
				<div class="itemRelAuthor"><?php echo JText::_("K2_BY"); ?> <a rel="author" href="<?php echo $item->author->link; ?>"><?php echo $item->author->name; ?></a></div>
				<?php endif; ?>

				<?php if($this->params->get('itemRelatedImageSize')): ?>
				<img style="width:<?php echo $item->imageWidth; ?>px;height:auto;" class="itemRelImg" src="<?php echo $item->image; ?>" alt="<?php $this->escape($item->title); ?>" />
				<?php endif; ?>

				<?php if($this->params->get('itemRelatedIntrotext')): ?>
				<div class="itemRelIntrotext"><?php echo $item->introtext; ?></div>
				<?php endif; ?>

				<?php if($this->params->get('itemRelatedFulltext')): ?>
				<div class="itemRelFulltext"><?php echo $item->fulltext; ?></div>
				<?php endif; ?>

				<?php if($this->params->get('itemRelatedMedia')): ?>
				<?php if($item->videoType=='embedded'): ?>
				<div class="itemRelMediaEmbedded"><?php echo $item->video; ?></div>
				<?php else: ?>
				<div class="itemRelMedia"><?php echo $item->video; ?></div>
				<?php endif; ?>
				<?php endif; ?>

				<?php if($this->params->get('itemRelatedImageGallery')): ?>
				<div class="itemRelImageGallery"><?php echo $item->gallery; ?></div>
				<?php endif; ?>
			</li>
			<?php endforeach; ?>
			<li class="clr"></li>
		</ul>
		<div class="clr"></div>
	</div>
	<?php endif; ?>

	<div class="clr"></div>

  <?php if(count($this->item->media)): ?>
  <!-- Item media -->
  <a name="itemMediaAnchor" id="itemMediaAnchor"></a>
  
  <div class="itemMediaBlock">
  	<h3><?php echo JText::_('K2_MEDIA'); ?></h3>
  	<?php foreach ($this->item->media as $entry) : ?>
	<div class="itemMedia">
  	
	<span class="itemMediaOutput"><?php echo $entry->output; ?></span>

	<?php if(!empty($entry->caption)): ?>
	<span class="itemMediaCaption"><?php echo $entry->caption; ?></span>
	<?php endif; ?>

	<?php if(!empty($entry->credits)): ?>
	<span class="itemMediaCredits"><?php echo $entry->credits; ?></span>
	<?php endif; ?>

	  <div class="clr"></div>
  </div> 
	<?php endforeach; ?>
  </div>
	
  <?php endif; ?>

  <?php if(count($this->item->galleries)): ?>
  <!-- Item image galleries -->
  <a name="itemImageGalleriesAnchor" id="itemImageGalleriesAnchor"></a>
  
  <div class="itemImageGalleries">
  	<h3><?php echo JText::_('K2_IMAGE_GALLERIES'); ?></h3>
  	<?php foreach ($this->item->galleries as $gallery): ?>
  		<div class="itemImageGallery">
  			<?php echo $gallery->output; ?>
  		</div>
  	<?php endforeach; ?>
  </div>

  <?php endif; ?>

  <?php if(!$this->print && (isset($this->item->nextLink) || isset($this->item->previousLink))): ?>
  <!-- Item navigation -->
  <div class="itemNavigation">
  	<span class="itemNavigationTitle"><?php echo JText::_('K2_MORE_IN_THIS_CATEGORY'); ?></span>

		<?php if(isset($this->item->previousLink)): ?>
		<a class="itemPrevious" href="<?php echo $this->item->previousLink; ?>">
			&laquo; <?php echo $this->item->previousTitle; ?>
		</a>
		<?php endif; ?>

		<?php if(isset($this->item->nextLink)): ?>
		<a class="itemNext" href="<?php echo $this->item->nextLink; ?>">
			<?php echo $this->item->nextTitle; ?> &raquo;
		</a>
		<?php endif; ?>

  </div>
  <?php endif; ?>

  <!-- K2 Plugins: K2AfterDisplay -->
  <?php echo $this->item->events->K2AfterDisplay; ?>

  <!-- K2 Plugins: K2CommentsBlock -->
  <?php echo $this->item->events->K2CommentsBlock; ?>




	<div class="clr"></div>
	</div>
<!-- End K2 Item Layout -->
