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
