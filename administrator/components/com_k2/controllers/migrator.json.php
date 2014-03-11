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

/**
 * Migrator JSON controller.
 */

class K2ControllerMigrator extends JControllerLegacy
{

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 * Recognized key values include 'name', 'default_task', 'model_path', and
	 * 'view_path' (this list is not meant to be comprehensive).
	 *
	 * @since   12.2
	 */
	public function __construct($config = array())
	{
		$application = JFactory::getApplication();
		$user = JFactory::getUser();
		if ($application->isSite() || !$user->authorise('core.manage', 'com_installer'))
		{
			return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		parent::__construct($config);

		set_error_handler(array($this, 'error'));
		set_exception_handler(array($this, 'exception'));

		$this->response = new stdClass;
		$this->response->type = '';
		$this->response->id = 0;
		$this->response->status = '';
		$this->response->errors = array();
		$this->response->completed = 0;
		$this->response->failed = 0;
	}

	public function run()
	{
		if (JSession::checkToken())
		{
			set_time_limit(0);
			$application = JFactory::getApplication();
			$type = $application->input->get('type', '', 'word');
			$id = $application->input->get('id', 0, 'int');
			if (method_exists($this, $type))
			{
				$this->response->type = $type;
				call_user_func(array($this, $type), $id);
			}
			else
			{
				return JError::raiseError(404);
			}

		}
		else
		{
			$this->response->errors[] = JText::_('JINVALID_TOKEN');
			$this->response->failed = 1;
		}
		var_dump($this->response);
		die ;
		return $this;
	}

	private function error($code, $description, $file, $line)
	{
		switch ($code)
		{
			case E_ERROR :
				$message = 'Error['.$code.'] '.$description.'. Line '.$line.' in file '.$file;
				$type = 'error';
				$this->response->failed = 1;
				break;

			case E_WARNING :
				$message = 'Warning['.$code.'] '.$description.'. Line '.$line.' in file '.$file;
				$type = 'warning';
				break;

			case E_NOTICE :
				$message = 'Notice['.$code.'] '.$description.'. Line '.$line.' in file '.$file;
				$type = 'notice';
				break;

			default :
				$message = 'Uknown error type['.$code.'] '.$description.'. Line '.$line.' in file '.$file;
				$type = 'error';
				break;
		}
		$this->response->errors[] = $message;
	}

	private function exception($exception)
	{
		$this->response->failed = 1;
		$this->response->errors[] = $exception->getMessage();
	}

	private function attachments(Integer $id)
	{
		$step = 1;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from($db->quoteName('#__k2_v2_attachments'))->where($db->quoteName('id').' > '.$id)->order($db->quoteName('id'));
		$db->setQuery($query, 0, $step);
		$attachments = $db->loadObjectList();
		foreach ($attachments as $attachment)
		{
			if (JFile::exists(JPATH_SITE.'/media/k2/attachments/'.$attachment->filename))
			{
				if (!JFolder::exists(JPATH_SITE.'/media/k2/attachments/'.$attachment->itemID))
				{
					JFolder::create(JPATH_SITE.'/media/k2/attachments/'.$attachment->itemID);
				}
				JFile::move(JPATH_SITE.'/media/k2/attachments/'.$attachment->filename, JPATH_SITE.'/media/k2/attachments/'.$attachment->itemID.'/'.$attachment->filename);
			}
			$query = $db->getQuery(true);
			$query->insert($db->quoteName('#__k2_attachments'));
			$query->values((int)$attachment->id.','.(int)$attachment->itemID.','.$db->quote($attachment->title).','.$db->quote($attachment->titleAttribute).','.$db->quote($attachment->filename).','.$db->quote('').','.(int)$attachment->hits);
			$db->setQuery($query);
			$db->execute();
			$this->response->id = $attachment->id;
		}

		if (count($attachments) == 0)
		{
			$this->response->id = 0;
			$this->response->type = 'categories';
		}
	}

	private function categories(Integer $id)
	{
		$step = 1;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from($db->quoteName('#__k2_v2_categories'))->where($db->quoteName('id').' > '.$id)->order($db->quoteName('id'));
		$db->setQuery($query, 0, $step);
		$categories = $db->loadObjectList();
		foreach ($categories as $category)
		{
			$hasImage = false;
			$newCategoryId = $category->id == 1 ? 99999 : $category->id;
			$categoryParams = json_decode($category->params);
			$data = array();
			$data['id'] = '';
			$data['title'] = $category->name;
			$data['alias'] = $category->alias;
			$data['state'] = $category->trash ? -1 : $category->published;
			$data['access'] = $category->access;
			$data['description'] = $category->description;
			$data['parent_id'] = 1;
			if ($category->image && JFile::exists(JPATH_SITE.'/media/k2/categories/'.$category->image))
			{
				JFile::move(JPATH_SITE.'/media/k2/categories/'.$category->image, JPATH_SITE.'/media/k2/categories/'.md5('Image'.$newCategoryId->id));
				$hasImage = true;
			}
			$data['template'] = isset($categoryParams->theme) && $categoryParams->theme ? $categoryParams->theme : '';
			$data['inheritance'] = isset($categoryParams->inheritFrom) && $categoryParams->inheritFrom ? $categoryParams->inheritFrom : 0;
			$data['metadata'] = array();
			$data['metadata']['description'] = isset($categoryParams->catMetaDesc) && $categoryParams->catMetaDesc ? $categoryParams->catMetaDesc : '';
			$data['metadata']['keywords'] = isset($categoryParams->catMetaKey) && $categoryParams->catMetaKey ? $categoryParams->catMetaKey : '';
			$data['metadata']['robots'] = isset($categoryParams->catMetaRobots) && $categoryParams->catMetaRobots ? $categoryParams->catMetaRobots : '';
			$data['metadata']['author'] = isset($categoryParams->catMetaAuthor) && $categoryParams->catMetaAuthor ? $categoryParams->catMetaAuthor : '';
			$data['language'] = $category->language;

			$model = K2Model::getInstance('Categories');
			$model->setState('data', $data);
			if (!$model->save())
			{
				$this->response->errors[] = $model->getError();
				$this->response->failed = 1;
				return;
			}
			$image = new stdClass;
			$image->flag = $hasImage ? 1 : 0;
			$image = json_encode($image);
			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__k2_categories'))->set(array($db->quoteName('id').' = '.$newCategoryId, $db->quoteName('image').' = '.$db->quote($image), $db->quoteName('plugins').' = '.$db->quote($category->plugins), $db->quoteName('params').' = '.$db->quote($category->params)))->where($db->quoteName('id').' = '.$category->id);
			$db->setQuery($query);
			$db->execute();
			$this->response->id = $category->id;
		}

		if (count($categories) == 0)
		{
			$this->response->id = 0;
			$this->response->type = 'categoriestree';
		}
	}

	private function categoriestree(Integer $id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, parent')->from($db->quoteName('#__k2_v2_categories'))->order($db->quoteName('parent, ordering'));
		$db->setQuery($query);
		$categories = $db->loadObjectList();
		foreach ($categories as $category)
		{
			$srcId = $category->id == 1 ? 99999 : $category->id;
			$table = JTable::getInstance('Categories', 'K2Table');
			$table->load($srcId);
			if ($category->parent)
			{
				$parentId = $category->parent;
				if ($parentId == 1)
				{
					$parentId = 99999;
				}
			}
			else
			{
				$parentId = 1;
			}
			$table->setLocation($parentId, 'last-child');
			if (!$table->store())
			{
				$this->response->errors[] = $table->getError();
				$this->response->failed = 1;
				return;
			}
		}
		$this->response->id = 0;
		$this->response->type = 'comments';
	}

	private function comments(Integer $id)
	{
		$db = JFactory::getDbo();
		$query = 'INSERT INTO '.$db->quoteName('#__k2_comments').'
		('.$db->quoteName('id').','.$db->quoteName('itemId').','.$db->quoteName('userId').','.$db->quoteName('name').','.$db->quoteName('date').','.$db->quoteName('email').','.$db->quoteName('url').','.$db->quoteName('ip').','.$db->quoteName('hostname').','.$db->quoteName('text').','.$db->quoteName('state').') 
		SELECT 
		'.$db->quoteName('id').','.$db->quoteName('itemID').','.$db->quoteName('userID').','.$db->quoteName('userName').','.$db->quoteName('commentDate').','.$db->quoteName('commentEmail').','.$db->quoteName('commentURL').','.$db->quote('').','.$db->quote('').','.$db->quoteName('commentText').','.$db->quoteName('published').' 
		FROM '.$db->quoteName('#__k2_v2_comments');
		$db->setQuery($query);
		$db->execute();
		$this->response->id = 0;
		$this->response->type = 'extrafields';
	}

	private function extrafields(Integer $id)
	{
		$step = 1;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from($db->quoteName('#__k2_v2_extra_fields'))->where($db->quoteName('id').' > '.$id)->order($db->quoteName('id'));
		$db->setQuery($query, 0, $step);
		$fields = $db->loadObjectList();
		foreach ($fields as $field)
		{
			$json = json_decode($field->value);
			$object = is_array($json) && isset($json[0]) ? $json[0] : $json;

			$required = isset($object->required) ? $object->required : 0;

			// Build alias
			if (isset($object->alias) && !empty($object->alias))
			{
				$alias = $object->alias;
			}
			else
			{
				$filter = JFilterInput::getInstance();
				$alias = $filter->clean($field->name, 'WORD');
				if (!$alias)
				{
					$alias = 'extraField'.$field->id;
				}
			}

			if ($field->type == 'textfield')
			{
				$type = 'text';
				$value = new stdClass;
				$value->value = isset($object->value) ? $object->value : '';
			}
			else if ($field->type == 'textarea')
			{
				$type = 'textarea';
				$value = new stdClass;
				$value->rows = isset($object->rows) ? $object->rows : 0;
				$value->columns = isset($object->cols) ? $object->cols : 0;
				$value->value = isset($object->value) ? $object->value : '';
				$value->editor = isset($object->editor) ? $object->editor : 0;
			}
			else if ($field->type == 'select')
			{
				$type = 'select';
				$value = new stdClass;
				$value->options = array();
				foreach ($json as $option)
				{
					$value->options[] = $option->name;
				}
			}
			else if ($field->type == 'multipleSelect')
			{
				$type = 'select';
				$value = new stdClass;
				$value->multiple = 1;
				$value->options = array();
				foreach ($json as $option)
				{
					$value->options[] = $option->name;
				}
				if ($json[0]->showNull)
				{
					$value->null = 1;
				}
			}
			else if ($field->type == 'radio')
			{
				$type = 'radio';
				$value = new stdClass;
				$value->options = array();
				foreach ($json as $option)
				{
					$value->options[] = $option->name;
				}
			}
			else if ($field->type == 'link')
			{
				$type = 'link';
				$value = new stdClass;
				$value->text = $object->name;
				$value->url = $object->value;
				$value->target = $object->target;
			}
			else if ($field->type == 'labels')
			{
				$type = 'labels';
				$value = new stdClass;
				$value->value = isset($object->value) ? $object->value : '';
			}
			else if ($field->type == 'date')
			{
				$type = 'date';
				$value = new stdClass;
				$value->date = isset($object->value) ? $object->value : '';
			}
			else if ($field->type == 'image')
			{
				$type = 'image';
				$value = new stdClass;
				$value->src = isset($object->value) ? $object->value : '';
				$value->alt = '';
			}

			$value = json_encode($value);

			$query = $db->getQuery(true);
			$query->insert($db->quoteName('#__k2_extra_fields'));
			$query->values((int)$field->id.','.$db->quote($field->name).','.$db->quote($alias).','.$db->quote($value).','.(int)$required.','.$db->quote($type).','.(int)$field->group.','.(int)$field->published.','.(int)$field->ordering);
			$db->setQuery($query);
			$db->execute();
			$this->response->id = $field->id;
		}

		if (count($fields) == 0)
		{
			$this->response->id = 0;
			$this->response->type = 'extrafieldsgroups';
		}
	}

	private function extrafieldsgroups(Integer $id)
	{
		$step = 10;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from($db->quoteName('#__k2_v2_extra_fields_groups'))->where($db->quoteName('id').' > '.$id)->order($db->quoteName('id'));
		$db->setQuery($query, 0, $step);
		$groups = $db->loadObjectList();
		foreach ($groups as $group)
		{
			$query = $db->getQuery(true);
			$query->select($db->quoteName('id'))->from($db->quoteName('#__k2_v2_categories'))->where($db->quoteName('extraFieldsGroup').' = '.(int)$group->id);
			$db->setQuery($query);
			$categories = $db->loadColumn();

			$assignments = new stdClass;
			$assignments->mode = 'specific';
			$assignments->categories = $categories;
			$assignments->recursive = 0;
			$assignments = json_encode($assignments);

			$query = $db->getQuery(true);
			$query->insert($db->quoteName('#__k2_extra_fields_groups'));
			$query->values((int)$group->id.','.$db->quote($group->name).','.$db->quote('item').','.$db->quote($assignments));
			$db->setQuery($query);
			$db->execute();
			$this->response->id = $group->id;
		}

		if (count($groups) == 0)
		{
			$this->response->id = 0;
			$this->response->type = 'items';
		}
	}

	private function items(Integer $id)
	{
		$step = 1;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from($db->quoteName('#__k2_v2_items'))->where($db->quoteName('id').' > '.$id)->order($db->quoteName('id'));
		$db->setQuery($query, 0, $step);
		$items = $db->loadObjectList();
		foreach ($items as $item)
		{
			$data = array();
			$data['id'] = '';
			$data['title'] = $item->title;
			$data['alias'] = $item->alias;
			$data['state'] = $item->trash ? -1 : $item->published;
			$data['featured'] = $item->featured;
			$data['access'] = $item->access;
			$data['catid'] = $item->catid;
			if ($data['catid'] == 1)
			{
				$data['catid'] = 99999;
			}
			$data['introtext'] = $item->introtext;
			$data['fulltext'] = $item->fulltext;
			$data['ordering'] = $item->ordering;
			$data['featured_ordering'] = $item->featured_ordering;
			$data['created'] = $item->created;
			$data['created_by'] = $item->created_by;
			$data['created_by_alias'] = $item->created_by_alias;
			$data['modified'] = $item->modified;
			$data['modified_by'] = $item->modified_by;
			$data['publish_up'] = $item->publish_up;
			$data['publish_down'] = $item->publish_down;
			$data['metadata'] = array();
			$data['metadata']['description'] = $item->metadesc;
			$data['metadata']['keywords'] = $item->metakey;
			$data['metadata']['robots'] = '';
			$data['metadata']['author'] = '';
			$metadata = new JRegistry($item->metadata);
			$metadata = $metadata->toArray();
			foreach ($metadata as $key => $value)
			{
				if ($key == 'robots' || $key == 'author')
				{
					$data['metadata'][$key] = $value;
				}
			}
			$data['language'] = $item->language;

			$model = K2Model::getInstance('Items');
			$model->setState('data', $data);
			if (!$model->save())
			{
				$this->response->errors[] = $model->getError();
				$this->response->failed = 1;
				return;
			}

			$image = new stdClass;
			$image->caption = $item->image_caption;
			$image->credits = $item->image_credits;
			$image->flag = JFile::exists(JPATH_SITE.'/media/k2/items/src/'.md5('Image'.$item->id)) ? 1 : 0;
			$image = json_encode($image);

			$media = new stdClass;
			$media->url = '';
			$media->provider = '';
			$media->id = '';
			$media->embed = '';
			$media->caption = $item->video_caption;
			$media->credits = $item->video_credits;
			$media->upload = '';
			if (!empty($item->video))
			{
				if (substr($item->video, 0, 1) !== '{')
				{
					$media->embed = $item->video;
				}
				else
				{
					if (strpos($item->video, 'remote}'))
					{
						preg_match("#}(.*?){/#s", $item->video, $matches);

						if (substr($matches[1], 0, 4) != 'http')
						{
							$media->upload = basename($matches[1]);
							if (JFile::exists(JPATH_SITE.'/media/k2/videos/'.$media->upload))
							{
								if (!JFolder::exists(JPATH_SITE.'/media/k2/media'))
								{
									JFolder::create(JPATH_SITE.'/media/k2/media');
								}

								if (!JFolder::exists(JPATH_SITE.'/media/k2/media/'.$item->id))
								{
									JFolder::create(JPATH_SITE.'/media/k2/media/'.$item->id);
								}
								JFile::move(JPATH_SITE.'/media/k2/videos/'.$media->upload, JPATH_SITE.'/media/k2/media/'.$item->id.'/'.$media->upload);
							}
						}
						else
						{
							$media->url = $matches[1];
						}
					}
					else
					{
						preg_match("#}(.*?){/#s", $item->video, $matches);
						$media->id = $matches[1];
						$video = substr($item->video, 1);
						$media->provider = substr($video, 0, strpos($video, '}'));
					}
				}
			}
			$media = json_encode(array($media));

			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__k2_items'))->set(array($db->quoteName('image').' = '.$db->quote($image), $db->quoteName('media').' = '.$db->quote($media), $db->quoteName('params').' = '.$db->quote($category->params)))->where($db->quoteName('id').' = '.$category->id);
			$db->setQuery($query);
			$db->execute();
			$this->response->id = $category->id;

			// TODO : Don't forget item stats!

			$this->response->id = $item->id;
		}

		if (count($items) == 0)
		{
			$this->response->id = 0;
			$this->response->type = 'tags';
		}

	}

	private function tags(Integer $id)
	{
		$step = 10;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from($db->quoteName('#__k2_v2_tags'))->where($db->quoteName('id').' > '.$id)->order($db->quoteName('id'));
		$db->setQuery($query, 0, $step);
		$tags = $db->loadObjectList();
		foreach ($tags as $tag)
		{
			$alias = $tag->name;
			if (JFactory::getConfig()->get('unicodeslugs') == 1)
			{
				$alias = JFilterOutput::stringURLUnicodeSlug($alias);
			}
			else
			{
				$alias = JFilterOutput::stringURLSafe($alias);
			}
			if (trim($alias) == '')
			{
				$alias = uniqid();
			}
			$query = $db->getQuery(true);
			$query->select($db->quoteName('id'))->from($db->quoteName('#__k2_tags'))->where($db->quoteName('alias').' = '.$db->quote($this->alias));
			$db->setQuery($query);
			if ($db->loadResult())
			{
				$alias .= '_'.uniqid();
			}
			$query = $db->getQuery(true);
			$query->insert($db->quoteName('#__k2_tags'));
			$query->values((int)$tag->id.','.$db->quote($tag->name).','.$db->quote($alias).','.(int)$tag->published.','.$db->quote(''));
			$db->setQuery($query);
			$db->execute();
			$this->response->id = $tag->id;
		}

		if (count($tags) == 0)
		{
			$this->response->id = 0;
			$this->response->type = 'tagsxref';
		}
	}

	private function tagsxref(Integer $id)
	{
		$db = JFactory::getDbo();
		$query = 'INSERT INTO '.$db->quoteName('#__k2_tags_xref').'('.$db->quoteName('tagId').','.$db->quoteName('itemId').') SELECT '.$db->quoteName('tagID').','.$db->quoteName('itemID').' FROM '.$db->quoteName('#__k2_v2_tags_xref');
		$db->setQuery($query);
		$db->execute();
		$this->response->id = 0;
		$this->response->type = 'users';
	}

	private function users(Integer $id)
	{
		$step = 5;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from($db->quoteName('#__k2_v2_users'))->where($db->quoteName('id').' > '.$id)->order($db->quoteName('id'));
		$db->setQuery($query, 0, $step);
		$authors = $db->loadObjectList();
		foreach ($authors as $author)
		{
			$image = new stdClass;
			$image->flag = 0;
			if ($author->image)
			{
				$image->flag = 1;
				if (JFile::exists(JPATH_SITE.'/media/k2/users/'.$author->image))
				{
					JFile::move(JPATH_SITE.'/media/k2/users/'.$author->image, JPATH_SITE.'/media/k2/users/'.md5('Image'.$author->userID).'.jpg');
				}
			}
			$image = json_encode($image);
			$query = $db->getQuery(true);
			$query->insert($db->quoteName('#__k2_users'));
			$query->values((int)$author->userID.','.$db->quote($user->description).','.$db->quote($image).','.$db->quote($author->url).','.$db->quote($author->gender).','.$db->quote($author->notes).','.$db->quote('').','.$db->quote($author->ip).','.$db->quote($author->hostname).','.$db->quote($author->plugins));
			$db->setQuery($query);
			$db->execute();

			$query = $db->getQuery(true);
			$query->select('COUNT(*)')->from('#__k2_v2_items')->where($db->quoteName('created_by').' = '.(int)$author->userID);
			$db->setQuery($query);
			$items = $db->loadResult();

			$query = $db->getQuery(true);
			$query->select('COUNT(*)')->from('#__k2_v2_comments')->where($db->quoteName('userID').' = '.(int)$author->userID);
			$db->setQuery($query);
			$comments = $db->loadResult();

			$query = $db->getQuery(true);
			$query->insert($db->quoteName('#__k2_users_stats'));
			$query->values((int)$author->userID.','.(int)$items.','.(int)$comments);
			$db->setQuery($query);
			$db->execute();
			$this->response->id = $author->id;
		}

		if (count($authors) == 0)
		{
			$this->response->completed = 1;
		}

	}

}
