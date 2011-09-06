# Assets
Yet another simple assets manager, also works with KOstache ([Mustache](https://github.com/bobthecow/mustache.php)).

## Usage

Adding new asset:

	$assets->set('head.js.script', 'assets/js/script.js');
	             | group |  id  |

	$assets->set('script', 'assets/js/script.js');
	             |  id  |

- group - optional
- id - required, must be unique

Removing asset:

	$assets->remove('style');

Retrieving asset:

	$style = $assets->get('style');

	$id = $assets->id();
	$style = $id['style'];

Retrieving group:

	$head = $assets->get('head');
	$css = $assets->get('head.css');

	$head = $assets->head();
	$css = $head['css'];

Retriving assets in Mustache template:

	{{assets.style}} (won't work)

	{{assets.id.style}} ($assets->id()['style'])

	{{#assets.head.js}}{{.}}{{/assets.head.js}} (foreach($assets->head()['css']...))

Note: methods `set()` and `remove()` returns `Assets` object, allowing for method chaining.

## Examples

In view classes:

	class View_Layout extends Kostache_Layout
	{
		public function assets()
		{
			return Assets::factory()
				// css
				->set('head.css.style', 'assets/css/style.css')
				// js
				->set('head.js.modernizr', 'assets/js/modernizr.min.js')
				->set('jquery-cdn', '//ajax.googleapis.com/ajax/libs/jquery/1.6.3/jquery.min.js')
				->set('jquery', 'assets/js/jquery-1.6.3.min.js')
				->set('body.js.plugins', 'assets/js/plugins.js')
				->set('body.js.admin-script', 'assets/js/admin/script.js');
		}
	}

	class View_Home_Index extends View_Layout
	{
		public function assets()
		{
			return parent::assets()
				->remove('admin-script')
				->set('body.js.script', 'assets/js/script.js');
		}
	}

In template files:

	{{#assets.head.css}}
	<link rel="stylesheet" href="{{.}}">
	{{/assets.head.css}}

	{{#assets.head.js}}
	<script src="{{.}}"></script>
	{{/assets.head.js}}

	<body>
		(...)

		<script src="{{assets.id.jquery-cdn}}"></script>
		<script>window.jQuery || document.write('<script src="{{assets.id.jquery}}"><\/script>')</script>


		{{#assets.body.js}}
		<script src="{{.}}"></script>
		{{/assets.body.js}}
	</body>

## Using with Yaminify

1. Download [Yaminify-Assets](https://github.com/TdroL/kohana-yaminify-assets) module into `modules/yaminify-assets` and enable it in `bootstrap.php` (must be enabled **before** assets module).
2. In view classes use `Yassets::factory()` instead of `Assets::factory()`.
