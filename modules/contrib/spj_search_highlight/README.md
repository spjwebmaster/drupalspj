# Views Search Highlight

This D8 / D9 module illustrates how to implement search highlighting in
views-based searches (using exposed filters).

It is **not** a production-ready module. 

## How to use

* install module
* make a view with exposed filter on a text field
* in `view_search_highlight.module` replace 'my_search_demo', 'body', 
  and 'body_value' with your view name, field name, and exposed filter name
* clear cache
* navigate to the view, provide a filter value, submit, see the value
  highlighted in the view result

## Known shortcomings

* it doesn't deal with possible edge cases
* it only deals with keyword-based searches
* it assumes the exposed filter's operator is 'CONTAINS' (default setting)
* the view name, filter name, and field name are hardcoded