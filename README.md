# wp-model

Missing functionalities from model objects of WordPress.

## API

### Post

Extended wrapper of native post object of WordPress, [`WP_Post`](http://codex.wordpress.org/Class_Reference/WP_Post).

#### new Post($post), Post::create($post)

- **$post** *integer | WP_Post*  
  Post id or WP_Post object.

```php
$post = new WPModel\Post($post_id);

// same properties of WP_Post are accessible
echo $post->post_date;
echo $post->post_title;
echo $post->post_mime_type;
```

#### ->permalink, ->permalink()

Get permalink of the post.

**Returns**: *string*

```php
$post = new WPModel\Post($post_id);

echo $post->permalink;
```

#### ->children, ->children([$options])

Get child posts of the post.

- **$options** *array*
  - **$options['id']** *integer | array*  
    Filter by ids.
  - **$options['post_type']** *string | array*  
    Filter by post types.
  - **$options['post_mime_type']** *string | array*  
    Filter by post mime types.

**Returns**: *array*

```php
$post = new WPModel\Post($post_id);

$children = $post->children(array(
  'id' => array(10, 11, 12)
));
$attachments = $post->children(array(
  'post_type' => 'attachment'
));
$images = $post->children(array(
  'post_mime_type' => 'image'
));
```

#### ->terms, ->terms([$options])

Get terms attached to the post.

- **$options** *array*
  - **$options['taxonomy']** *string*  
    Specify taxonomy. Default value: `'category'`

**Returns**: *array*

```php
$post = new WPModel\Post($post_id);

$custom_terms = $post->terms(array(
  'taxonomy' => 'custom_taxonomy'
));
```

#### ->meta, ->meta()

PostMeta object of the post.

**Retusns**: *WPModel\PostMeta*

```php
$post = new WPModel\Post($post_id);

// get meta value
echo $post->meta->custom_field;

// set meta value
$post->meta->custom_field = 'hello';
```

#### ->image, ->image([$options])

Get WPModel\Image object if the post is an image attachment.

- **$options** *array*
  - **$options['size']** *string*  
    Specify size by string (e.g. `'full'`, `'large'`, `'medium'`, `'thumbnail'`). Default value: `'full'`

**Returns**: *WPModel\Image*

```php
$post = new WPModel\Post($post_id);

$thumbnail = $post->image(array(
  'size' => 'thumbnail'
));
echo $thumbnail->url;
echo $thumbnail->path;
echo $thumbnail->width;
echo $thumbnail->height;
```

#### ->images, ->images()

Get images array if the post is an image attachment.

**Returns**: *array*

```php
$post = new WPModel\Post($post_id);

$images = $post->images;

// get WPModel\Image object
$images['full'];
$images['large'];
$images['medium'];
$images['thumbnail'];
```

#### ->group, ->group([$options])

Retrieve posts grouped by same terms.

- **$options** *array*
  - **$options['taxonomy']** *string*  
    Taxonomy name. Default value: `'category'`
  - **$options['options']** *array*  
    Custom arguments for `WP_Query` that is internally called.
  - **$options['map']** *callable*  
    Default value: `array('WPModel\Post', 'create')`

**Returns**: *array*

```php
$post = new WPModel\Post($post_id);

$related_posts = $post->group(array(
  'taxonomy' => 'custom_taxonomy',
  'options' => array('posts_per_page' => 5),
  'map' => function($post) {
    return CustomPostClass($post);
  },
));
```

#### ->neighbor($options)

Retrieve a prev/next `WP_Post` object. Returns `null` if it doesn’t exist.

- **$options** *array*
  - **$options['post_type']** *string* | *array*  
    Post type. Default value: `$this->post_type`
  - **$options['direction']** *string*  
    Direction of neighbor: `prev` or `next`

**Returns**: *WP_Post*

```php
$post = new WPModel\Post($post_id);

$prev_post = $post->neighbor(array(
  'post_type' => ['post', 'custom_post_type'],
  'direction' => 'prev',
));
```

#### ->exists, ->exists()

Return `true` if the post exists.

**Returns**: *boolean*

```php
$post = new WPModel\Post(0);

$post->exists; // => false
```

#### ->hasChild($post)

Return `true` if the value is a child of the post.

- **$post** *integer | WP_Post | WPModel\Post*

**Returns**: *boolean*

```php
$post = new WPModel\Post($post_id);

$post->hasChild($child_post);
```

#### ->matchMimeType($type)

Return `true` if the value matches mime type of the post. For example, `'image/jpeg'`, `'image'` and `'jpeg'` match `'image/jpeg'`.

- **$type** *string*

**Retusns**: *boolean*

```php
$post = new WPModel\Post($post_id);

$post->matchMimeType('image/jpeg');
```

### PostMeta

See `->meta` of `WPModel\Post`.

### Term

Extended wrapper of native term object of WordPress. See return values of [`wp_get_post_terms`](http://codex.wordpress.org/Function_Reference/wp_get_post_terms).

#### new Term($term, [$taxonomy]), Term::create($term, [$taxonomy])

- **$term** *integer | term object*  
  Term id or term object.
- **$taxonomy** *string*  
  Taxonomy name.

```php
// create by constructor
$term = new WPModel\Term($term_id, 'custom_taxonomy');

// get from WPModel\Post
$post = new WPModel\Post($post_id);
$terms = $post->terms(array(
  'taxonomy' => 'custom_taxonomy'
));
```

#### ->children, ->children([$options])

Get child terms of the term.

- **$options** *array*  
  See `$args` of [`get_terms`](http://codex.wordpress.org/Function_Reference/get_terms).

```php
$child_terms = $term->children(array(
  'orderby' => 'count',
  'hide_empty' => false
));
```

### Image

See `->image` of `WPModel\Post`.

### User

Extended wrapper of native user object of WordPress, [`WP_User`](http://codex.wordpress.org/Class_Reference/WP_User).

#### new User([$user]), User::create([$user])

- **$user** *integer | WP_User*  
  User id or WP_User object. If not specified, returns current user.

```php
// current user
$user = new WPModel\User();

// specify user id
$user = new WPModel\User($user_id);

// same properties of WP_User are accessible
echo $user->user_email;
echo $user->user_login;
echo $user->first_name;
```

#### ->meta, ->meta()

UserMeta object of the user.

**Returns**: *WPModel\UserMeta*

```php
$user = new WPModel\User();

// get meta value
echo $user->meta->rich_editing;

// set meta value
$user->meta->rich_editing = 'false';
```

#### ->exists, ->exists()

Return `true` if the user exists.

**Returns**: *boolean*

```php
$post = new WPModel\User();

$post->exists; // => true
```

### UserMeta

See `->meta` of `WPModel\User`.
