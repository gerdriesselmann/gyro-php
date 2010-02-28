<?php
/**
 * @defgroup SocialBookmarking
 * 
 * Adds some social bookmarking icons to your site, including "Mail to a friend"
 * 
 * @section Usage Usage
 * 
 * Use WidetBookmarking to output the icons. The module provides a lot of services 
 * and sets of them. See SocialBookmarking class for details.
 * 
 * @code
 * print WidgetBookmarking::ouput($page_data, SocialBookmarking::SET_POPULAR_EN);
 * // prints icons for popular bookmarking services in US
 * print WidgetBookmarking::ouput($page_data, array(
 *   SocialBookmarking::EMAIL,
 *   SocialBookmarking::DIGG,
 *   SocialBookmarking::TWITTER
 * );
 * // prints the three desired bookmarks
 * @endcode
 */