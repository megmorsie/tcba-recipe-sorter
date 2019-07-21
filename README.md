# TCBA Recipe Sorter Plugin

Installation
------
1. Requires **WP Recipe Maker** plugin to be installed and active. Otherwise, shortcode will return an error message (see Troubleshooting/Known Issues section below for details).
2. To install TCBA Recipe Sorter, download a **zipped file** of this repository and **upload to WordPress**.

Usage
------
To display the filtering options and search results, put this shortcode on a page/post:
`[recipe-sorter]`

Front-End Display
------
![](recipe-filter-pea.gif)

Troubleshooting/Known Issues
------
In the limited scope/timeframe of this weekend, **some of the functionality isn't ideal or is unfinished**.

**Error Messages & Meanings**
"Please ensure the WP Recipe Maker plugin is active. It is required for the TCBA Recipe Sorter plugin to work."
- This message will display if the plugin WP Recipe Maker is not installed and active.
- WP Recipe Maker should be located in the plugins directory under `wp-recipe-maker-premium/wp-recipe-maker-premium.php`.

"We do not currently have any recipes matching the criteria. Please adjust the search criteria above."
- This message will display if the combination of all of the search filters return no results. As more recipes are added, this message should be less common. Users can simplify or adjust their search and try again.
- The "Ingredient Search" text field only searches title and content, not the meta field for ingredient. For example, if you search "apples" instead of "apple," the Apple Chips recipe will not display. (See 4 under FIXMEs.)

# FIXME (Cleveland GiveCamp Project)
Remaining Work in `cgc-testing-template.php`
1. Refactor as much as possible.
2. Sanitize any fields needing it.
3. Duplicate taxonomy functionality for other ones...
Available taxonomies that were left out of scope: `wprm_cuisine`, `wprm_ingredient`, `wprm_method`*, `wprm_style`*, `wprm_type`*
Taxonomies with asterisks were added specifically for TCBA.
4. Install Relevanssi & work functionality into search so that meta fields are included (`wprm_ingredients`).

Stuff I Need
1. Help refactoring - I'm repeating myself a lot.
2. Help checking for missing sanitization/doing it better.
