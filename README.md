# wp-model

Missing functionalities from model objects of WordPress.

## Post

Extended wrapper of native post object of WordPress, [`WP_Post`](http://codex.wordpress.org/Class_Reference/WP_Post).

```php
// create \WPModel\Post object
// $p is WP_Post object or post id
$post = new \WPModel\Post($p);

// call any property of WP_Post
$date = $post->post_date;
$title = $post->post_title;
$content = $post->post_content;

// get permalink
$permalink = $post->permalink;

// get child posts (returns array of \WPModel\Post objects)
$children = $post->children;

// filter child posts by post_type
$attachments = $post->children(array('post_type' => 'attachment'));

// filter child posts by post_mime_type
$jpeg_images = $post->children(array('post_mime_type' => 'image/jpeg'));
$jpeg_images = $post->children(array('post_mime_type' => 'jpeg'));
$images = $post->children(array('post_mime_type' => 'image'));

// filter child posts by ids
$children_by_ids = $post->children(array('id' => array(100, 101, 102)));

// get category terms (returns array of \WPModel\Term objects)
$category_terms = $post->terms;

// get terms of custom taxonomy
$custom_taxonomy_terms = $post->terms(array('taxonomy' => 'custom_taxonomy'));

// get custom field value
$custom_field_value = $post->meta->custom_field_key;
$custom_field_value = $post->meta['custom_field_key'];

// set custom field value
$post->meta->custom_field_key = 'custom_field_value';
$post->meta['custom_field_key'] = 'custom_field_value';

// get image source object (returns \WPModel\Image object)
$image_source = $post->image;
$image_source = $post->image(array('size' => 'thumbnail'));
// get image properties
$image_url = $image_source->url;
$image_width = $image_source->width;
$image_height = $image_source->height;
```

## Term

Wrapper of native term object of WordPress. See return values of [`wp_get_post_terms`](http://codex.wordpress.org/Function_Reference/wp_get_post_terms).

## Image

See `Post`.
