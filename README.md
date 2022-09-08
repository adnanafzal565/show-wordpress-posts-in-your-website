# Show WordPress posts in your website

To show WordPress blog posts in your website, make sure you run this code from inside your server where the WordPress blog is hosted. This means that if your WordPress blog is hosted on the main domain like "adnan-tech.com", then this code should be on any of the sub-domain like "web.adnan-tech.com".

And vice versa, if your WordPress blog is hosted on a sub-domain like "blog.adnan-tech.com", then this code should be on the main domain like "adnan-tech.com". This script will not work on localhost because many of the WordPress blogs have some security plugins installed, that prevent any external host to fetch data from it.

To show all the blog posts from your WordPress website, we will be using WordPress posts API. Just write the following code in the file where you want to show the blog listing. Make sure to change your blog URL in **$blog_api_url** variable:

```php
<?php
    $blog_api_url = "https://yourblogdomain/wp-json/wp/v2";

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $blog_api_url . "/posts",
        CURLOPT_RETURNTRANSFER => 1
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    
    $posts = json_decode($response);
    foreach ($posts as $post)
    {
        $id = $post->id;
        $date = date("d M, Y", strtotime($post->date));
        $slug = $post->slug;
        $status = $post->status; // "publish"
        if ($status != "publish")
        {
            continue;
        }
        $link = $post->link;
        $title = $post->title->rendered;
        $excerpt = $post->excerpt->rendered;
    
        $curlMedia = curl_init();
        curl_setopt_array($curlMedia, [
            CURLOPT_URL => $blog_api_url . "/media/" . $post->featured_media,
            CURLOPT_RETURNTRANSFER => 1
        ]);
        $responseMedia = curl_exec($curlMedia);
    
        $responseMedia = json_decode($responseMedia);
        $mediaTitle = $responseMedia->title->rendered;
        $altText = $responseMedia->alt_text;
        $mediaSourceUrl = $responseMedia->source_url;

        ?>

        <div style="border: 1px solid black; margin: 10px; padding: 10px;">
            <?php if (!empty($mediaSourceUrl)) { ?>
                <img src="<?php echo $mediaSourceUrl; ?>" alt="<?php echo $altText; ?>" title="<?php echo $mediaTitle; ?>" style="width: 100%;" />
            <?php } ?>
            
            <div>
                <p><?php echo $date; ?></p>
                <h2>
                    <a href="./show-post.php?slug=<?php echo $slug; ?>">
                        <?php echo $title; ?>
                    </a>
                </h2>
                <p><?php echo $excerpt; ?></p>
            </div>
        </div>

    <?php
    }
?>
```

This will fetch the latest posts from your WordPress blog. We are looping through all the posts and saving the necessary variables from the ***$post*** object. You can write ***print_r($post)*** inside the ***foreach*** loop to view an entire ***$post*** object. We are only displaying the published posts here. If you want to show the unpublished posts as well, simply comment out the continue; statement. Inside the ***foreach*** loop, we are calling another CURL request to get the featured image of the post.

## Blog listing

Then, we are creating a &lt;div> inside the foreach loop with a 1-pixel black border and a 10 pixels margin from all sides. We are also giving it a 10 pixels padding so it will margin from inside too. Inside this div, we display the post's published date, title, and excerpt. An excerpt is a short text from the blog post, you can set it up from the WordPress admin panel for each post separately. If not specified, then it will be the first paragraph of the post.
    
We are making the title a hyperlink, so when clicked, it will redirect the user to a new page where we can show that post in detail. We are using "./" in the hyperlink because our post detail page is in the same directory. You can also redirect the user to the original post on the WordPress blog using the variable $link. We didn't put the hyperlink on the image, because if the post does not have a featured image, then the link will not be created. And the user will have no option to redirect to the post detail page.

# Single post
    
To show the post detail inside your website, simply create a file named show-post.php and write the following code in it:

```php
<?php
    $slug = $_GET["slug"];
    $blog_api_url = "https://yourblogdomain/wp-json/wp/v2";

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $blog_api_url . "/posts?slug=" . $slug,
        CURLOPT_RETURNTRANSFER => 1
    ]);
    $response = curl_exec($curl);
    curl_close($curl);

    $posts = json_decode($response);
    if (count($posts) == 0)
    {
        die("Post not found.");
    }
    
    $date = date("d M, Y H:i:s", strtotime($posts[0]->date));
    $status = $posts[0]->status; // "publish"
    if ($status != "publish")
    {
        die("Post not published.");
    }
    $title = $posts[0]->title->rendered;
    $content = $posts[0]->content->rendered;

    $curlMedia = curl_init();
    curl_setopt_array($curlMedia, [
        CURLOPT_URL => $blog_api_url . "/media/" . $posts[0]->featured_media,
        CURLOPT_RETURNTRANSFER => 1
    ]);
    $responseMedia = curl_exec($curlMedia);

    $responseMedia = json_decode($responseMedia);
    $mediaTitle = $responseMedia->title->rendered;
    $altText = $responseMedia->alt_text;
    $mediaSourceUrl = $responseMedia->source_url;
?>

<!-- Post title -->
<h1><?php echo $title; ?></h1>

<!-- Post meta content -->
<p>Posted on <?php echo $date; ?></p>

<!-- Preview featured image -->
<?php if (!empty($mediaSourceUrl)) { ?>
    <img src="<?php echo $mediaSourceUrl; ?>" alt="<?php echo $altText; ?>" title="<?php echo $mediaTitle; ?>" style="width: 100%;" />
<?php } ?>

<!-- Post content-->
<section>
    <?php echo $content; ?>
</section>
```

This will show the post title in the heading. Date the post was published. Featured image if available, the featured image will also have an alt text that will be displayed in case the image is not found. It will also have a title attribute that will be visible when the user moves the mouse over the image (hover effect). Finally, we are displaying the complete content of the post.

## Show comments of the post

To show the comments of the post, first, we need to fetch all the comments of the post. We are again going to use the CURL request on WordPress comments API. Write the following code below post content:

```php
<?php
    $curlComments = curl_init();
    curl_setopt_array($curlComments, [
        CURLOPT_URL => $blog_api_url . "/comments/?slug=" . $posts[0]->slug,
        CURLOPT_RETURNTRANSFER => 1
    ]);
    $responseComments = curl_exec($curlComments);
    $responseComments = json_decode($responseComments);

    foreach ($responseComments as $comment) {
        if ($comment->status != "approved") {
            continue;
        }
?>
    <div style="border: 1px solid black; margin: 10px; padding: 10px;">
        <?php foreach (((array) $comment->author_avatar_urls) as $avatar_url) { ?>
            <img src="<?php echo $avatar_url; ?>" style="width: 100px;" />
        <?php break; } ?>

        <p><?php echo $comment->author_name; ?></p>
        <p><?php echo $comment->content->rendered; ?></p>
    </div>
<?php } ?>
```

This will first fetch all the comments of that post. Then it will loop through each comment and will skip the loop iteration if the comment is not approved yet. Then, it will create a &lt;div> tag with styles the same as we did for the blog listing. The ***author_avatar_urls*** object in each ***$comment*** is an object. So we need to convert that to an array using (array) typecasting. Then we will loop through it, and display the profile image of the user who posted that comment. After that, we are using a break statement so it will not show multiple images, it will stop the loop after displaying one image. User profile images are in different dimensions, but we need to show only one. And finally, we display the name of the person who posted the comment and the comment itself.
