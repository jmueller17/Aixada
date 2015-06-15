The tool [jqGrid](https://github.com/tonytomov/jqGrid) is used to manage the grids and forms by the `manage_data.php` of Aixada.

To find out what options are available see [jqGrid documentation](http://www.trirand.com/jqgridwiki/doku.php)

The presentation `manage_data.php` assumes some default values in the fields definitions:
  * If `xmlmap`, `index` or `label` are not specified the value is taken from `name`.
  * Assumes `editable:true`
  * If specified `width` is assumed `hidden:false` conversely when they are not specified it is assumed `hidden:true`

See also: [fields definitions options](http://www.trirand.com/jqgridwiki/doku.php?id=wiki:common_rules)
