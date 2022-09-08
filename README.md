# Show WordPress posts in your website

To show WordPress blog posts in your website, make sure you run this code from inside your server where the WordPress blog is hosted. This means that if your WordPress blog is hosted on the main domain like "adnan-tech.com", then this code should be on any of the sub-domain like "web.adnan-tech.com".

And vice versa, if your WordPress blog is hosted on a sub-domain like "blog.adnan-tech.com", then this code should be on the main domain like "adnan-tech.com". This script will not work on localhost because many of the WordPress blogs have some security plugins installed, that prevent any external host to fetch data from it.

To show all the blog posts from your WordPress website, we will be using WordPress posts API. Just write the following code in the file where you want to show the blog listing. Make sure to change your blog URL in **$blog_api_url** variable:

```
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
