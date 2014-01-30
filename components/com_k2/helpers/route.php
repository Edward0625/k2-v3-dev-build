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
 * K2 route helper class.
 */
class K2HelperRoute
{

	private static $cache = array('item' => array(), 'category' => array(), 'user' => array(), 'tag' => array());

	public static function getItemRoute($id, $category)
	{
		// Get application
		$application = JFactory::getApplication();

		// Get component menu links
		$component = JComponentHelper::getComponent('com_k2');
		$menu = $application->getMenu('site');
		$items = $menu->getItems('component_id', $component->id);

		// Initialze route
		$route = 'index.php?option=com_k2&view=item&id='.$id;

		// Cast variables
		$id = (int)$id;
		$category = (int)$category;

		// Search only if we have not the item in our cache
		if (!isset(self::$cache['item'][$id]))
		{
			// Initialize Itemid
			$Itemid = '';

			// Search the menu
			foreach ($items as $item)
			{
				if ($item->query['view'] == 'item' && $item->query['id'] == $id)
				{
					$Itemid = $item->id;
					break;
				}
			}

			// If we do not have menu link to the item search for a menu link to it's category
			if (!$Itemid)
			{
				foreach ($items as $item)
				{
					if ($item->query['view'] == 'itemlist' && isset($item->query['task']) && $item->query['task'] == 'category' && isset($item->query['id']) && $item->query['id'] == $category)
					{
						$Itemid = $item->id;
						break;
					}
				}
			}

			// Second pass for menu links to multiple categories
			if (!$Itemid)
			{
				foreach ($items as $item)
				{
					if ($item->query['view'] == 'itemlist' && isset($item->query['task']) && $item->query['task'] == 'category' && !isset($item->query['id']))
					{
						// Get menu link categories
						$filter = $item->params->get('categories');
						if (isset($filter->categories) && is_array($filter->categories) && in_array($category, $filter->categories))
						{
							$Itemid = $item->id;
							break;
						}
					}
				}
			}

			// Add what we found to cache
			self::$cache['item'][$id] = $Itemid;
		}

		// Append what we have found
		$route .= '&Itemid='.self::$cache['item'][$id];

		return $route;
	}

	public static function getCategoryRoute($id)
	{
		// Get application
		$application = JFactory::getApplication();

		// Get component menu links
		$component = JComponentHelper::getComponent('com_k2');
		$menu = $application->getMenu('site');
		$items = $menu->getItems('component_id', $component->id);

		// Initialze route
		$route = 'index.php?option=com_k2&view=itemlist&task=category&id='.$id;

		// Cast variables
		$id = (int)$id;

		// Search only if we have not the item in our cache
		if (!isset(self::$cache['category'][$id]))
		{
			// Initialize Itemid
			$Itemid = '';

			// If we do not have menu link to the item search for a menu link to it's category
			foreach ($items as $item)
			{
				if ($item->query['view'] == 'itemlist' && isset($item->query['task']) && $item->query['task'] == 'category' && isset($item->query['id']) && $item->query['id'] == $id)
				{
					$Itemid = $item->id;
					break;
				}
			}

			// Second pass for menu links to multiple categories
			if (!$Itemid)
			{
				foreach ($items as $item)
				{
					if ($item->query['view'] == 'itemlist' && isset($item->query['task']) && $item->query['task'] == 'category' && !isset($item->query['id']))
					{
						// Get menu link categories
						$filter = $item->params->get('categories');
						if (isset($filter->categories) && is_array($filter->categories) && in_array($id, $filter->categories))
						{
							$Itemid = $item->id;
							break;
						}
					}
				}
			}

			// Add what we found to cache
			self::$cache['category'][$id] = $Itemid;
		}

		// Append what we have found
		$route .= '&Itemid='.self::$cache['category'][$id];

		return $route;

	}

	public static function getUserRoute($id)
	{
		// Get application
		$application = JFactory::getApplication();

		// Get component menu links
		$component = JComponentHelper::getComponent('com_k2');
		$menu = $application->getMenu('site');
		$items = $menu->getItems('component_id', $component->id);

		// Initialze route
		$route = 'index.php?option=com_k2&view=itemlist&task=user&id='.$id;

		// Cast variables
		$id = (int)$id;

		// Search only if we have not the item in our cache
		if (!isset(self::$cache['user'][$id]))
		{
			// Initialize Itemid
			$Itemid = null;

			// If we do not have menu link to the item search for a menu link to it's category
			foreach ($items as $item)
			{
				if ($item->query['view'] == 'itemlist' && isset($item->query['task']) && $item->query['task'] == 'user' && $item->query['id'] == $id)
				{
					$Itemid = $item->id;
					break;
				}
			}

			// Add what we found to cache
			self::$cache['user'][$id] = $Itemid;
		}

		// Append what we have found
		$route .= '&Itemid='.self::$cache['user'][$id];

		return $route;

	}

	public static function getTagRoute($id)
	{
		// Get application
		$application = JFactory::getApplication();

		// Get component menu links
		$component = JComponentHelper::getComponent('com_k2');
		$menu = $application->getMenu('site');
		$items = $menu->getItems('component_id', $component->id);

		// Initialze route
		$route = 'index.php?option=com_k2&view=itemlist&task=tag&id='.$id;

		// Cast variables
		$id = (int)$id;

		// Search only if we have not the item in our cache
		if (!isset(self::$cache['tag'][$id]))
		{
			// Initialize Itemid
			$Itemid = '';

			// If we do not have menu link to the item search for a menu link to it's category
			foreach ($items as $item)
			{
				if ($item->query['view'] == 'itemlist' && isset($item->query['task']) && $item->query['task'] == 'tag' && $item->query['id'] == $id)
				{
					$Itemid = $item->id;
					break;
				}
			}

			// Add what we found to cache
			self::$cache['tag'][$id] = $Itemid;
		}

		// Append what we have found
		$route .= '&Itemid='.self::$cache['tag'][$id];

		return $route;
	}

	public static function getDateRoute($year, $month, $day = null, $category = null)
	{
		$route = 'index.php?option=com_k2&view=itemlist&task=date&year='.$year.'&month='.$month;
		if ($day)
		{
			$route .= '&day='.$day;
		}
		if ($category)
		{
			$route .= '&category='.$category;
		}
		return $route;
	}

	public static function getSearchRoute()
	{
		return 'index.php?option=com_k2&view=itemlist&task=search';
	}

}
