<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Blog Home - Start Bootstrap Template</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="blog-home/assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="blog-home/css/styles.css" rel="stylesheet" />
    </head>
    <body>
        <!-- Responsive navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="#!">Start Bootstrap</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="#!">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="#!">Contact</a></li>
                        <li class="nav-item"><a class="nav-link active" aria-current="page" href="#">Blog</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Page header with logo and tagline-->
        <header class="py-5 bg-light border-bottom mb-4">
            <div class="container">
                <div class="text-center my-5">
                    <h1 class="fw-bolder">Welcome to Blog Home!</h1>
                    <p class="lead mb-0">A Bootstrap 5 starter layout for your next blog homepage</p>
                </div>
            </div>
        </header>
        <!-- Page content-->
        <div class="container">
            <div class="row">
                <!-- Blog entries-->
                <div class="col-lg-8">
                    
                    <?php
                    
                    $blog_api_url = "https://yourblogdomain/wp-json/wp/v2";
    
                    $curl = curl_init();
                    curl_setopt_array($curl, [
                        CURLOPT_URL => $blog_api_url . "/posts",
                        CURLOPT_RETURNTRANSFER => 1
                    ]);
                    $response = curl_exec($curl);
                    curl_close($curl);
                    
                    // header("Content-Type: application/json");
                    // echo json_encode(json_decode($response), JSON_PRETTY_PRINT);
                    
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
                    
                        <!-- Featured blog post-->
                        <div class="card mb-4">
                            <?php if (!empty($mediaSourceUrl)) { ?>
                                <img class="card-img-top" src="<?php echo $mediaSourceUrl; ?>" alt="<?php echo $title; ?>" title="<?php echo $mediaTitle; ?>" />
                            <?php } ?>
                            
                            <div class="card-body">
                                <div class="small text-muted"><?php echo $date; ?></div>
                                <h2 class="card-title">
                                    <a href="./show-post.php?slug=<?php echo $slug; ?>">
                                        <?php echo $title; ?>
                                    </a>
                                </h2>
                                <p class="card-text"><?php echo $excerpt; ?></p>
                            </div>
                        </div>
                        <!-- Nested row for non-featured blog posts-->
                    
                    <?php } ?>
                    
                </div>
                <!-- Side widgets-->
                <div class="col-lg-4">
                    <!-- Search widget-->
                    <div class="card mb-4">
                        <div class="card-header">Search</div>
                        <div class="card-body">
                            <div class="input-group">
                                <input class="form-control" type="text" placeholder="Enter search term..." aria-label="Enter search term..." aria-describedby="button-search" />
                                <button class="btn btn-primary" id="button-search" type="button">Go!</button>
                            </div>
                        </div>
                    </div>
                    <!-- Categories widget-->
                    <div class="card mb-4">
                        <div class="card-header">Categories</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <ul class="list-unstyled mb-0">
                                        <li><a href="#!">Web Design</a></li>
                                        <li><a href="#!">HTML</a></li>
                                        <li><a href="#!">Freebies</a></li>
                                    </ul>
                                </div>
                                <div class="col-sm-6">
                                    <ul class="list-unstyled mb-0">
                                        <li><a href="#!">JavaScript</a></li>
                                        <li><a href="#!">CSS</a></li>
                                        <li><a href="#!">Tutorials</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Side widget-->
                    <div class="card mb-4">
                        <div class="card-header">Side Widget</div>
                        <div class="card-body">You can put anything you want inside of these side widgets. They are easy to use, and feature the Bootstrap 5 card component!</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer-->
        <footer class="py-5 bg-dark">
            <div class="container"><p class="m-0 text-center text-white">Copyright &copy; Your Website 2022</p></div>
        </footer>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="blog-home/js/scripts.js"></script>
    </body>
</html>