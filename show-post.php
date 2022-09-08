<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Blog Post - Start Bootstrap Template</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="blog-post/assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="blog-post/css/styles.css" rel="stylesheet" />
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
        <!-- Page content-->
        <div class="container mt-5">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Post content-->
                    <article>
                        
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
                            
                            // print_r($posts[0]);exit;
                        
                            $post = new \stdClass();
                            
                            $date = date("d M, Y", strtotime($posts[0]->date));
                            $slug = $posts[0]->slug;
                            $status = $posts[0]->status; // "publish"
                            if ($status != "publish")
                            {
                                die("Post not published.");
                            }
                            $link = $posts[0]->link;
                            $title = $posts[0]->title->rendered;
                            $excerpt = $posts[0]->excerpt->rendered;
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
                        
                            $curlComments = curl_init();
                            curl_setopt_array($curlComments, [
                                CURLOPT_URL => $blog_api_url . "/comments/?slug=" . $posts[0]->slug . "&status=approve",
                                CURLOPT_RETURNTRANSFER => 1
                            ]);
                            $responseComments = curl_exec($curlComments);
                            $responseComments = json_decode($responseComments);
                        ?>
                        
                        <!-- Post header-->
                        <header class="mb-4">
                            <!-- Post title-->
                            <h1 class="fw-bolder mb-1"><?php echo $title; ?></h1>
                            <!-- Post meta content-->
                            <div class="text-muted fst-italic mb-2">Posted on <?php echo $date; ?></div>
                        </header>
                        <!-- Preview image figure-->
                        <figure class="mb-4">
                            <?php if (!empty($mediaSourceUrl)) { ?>
                                <img class="img-fluid rounded" src="<?php echo $mediaSourceUrl; ?>" alt="<?php echo $altText; ?>" title="<?php echo $mediaTitle; ?>" />
                            <?php } ?>
                        </figure>
                        <!-- Post content-->
                        <section class="mb-5">
                            <?php echo $content; ?>
                        </section>
                    </article>
                    <!-- Comments section-->
                    <section class="mb-5">
                        <div class="card bg-light">
                            <div class="card-body">
                                <?php
                                
                                    foreach ($responseComments as $comment) {
                                        if ($comment->status != "approved") {
                                            continue;
                                        }
                                ?>
                                    <div class="d-flex mb-4">
                                        <div class="flex-shrink-0">
                                            <?php foreach (((array) $comment->author_avatar_urls) as $avatar_url) { ?>
                                                <img class="rounded-circle" src="<?php echo $avatar_url; ?>" />
                                            <?php break; } ?>
                                        </div>
                                        <div class="ms-3">
                                            <div class="fw-bold"><?php echo $comment->author_name; ?></div>
                                            <?php echo $comment->content->rendered; ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </section>
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
        <script src="blog-post/js/scripts.js"></script>
    </body>
</html>
    