<?php

header('Access-Control-Allow-Origin: *');

$id1 = array(
    "title" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. 21st-century, quam, velit!",
    "content" => "Lorem ipsum dolor sit amet, <strong>consectetur adipisicing</strong> elit. Eveniet libero magni modi molestias, nihil quidem sit. <a href=\"#\">Architecto</a> at consequuntur est fuga harum impedit, laboriosam nemo odio, quaerat saepe totam voluptates?
<h2>Nihil quidem sit. Architecto at consequuntur</h2>
Lorem ipsum dolor sit amet, consectetur adipisicing elitLorem ipsum dolor sit amet, consectetur adipisicing elitLorem ipsum dolor sit amet, consectetur adipisicing <a href=\"#\">elitLorem ipsum dolor sit</a> amet, consectetur adipisicing elit.consectetur adipisicing elitLorem ipsum dolor sit amet, consectetur adipisicing elitLorem ipsum dolor sit amet, consectetur adipisicing elit.
<h3>A - Consectetur adipisicing elitLorem ipsum</h3>
Lorem ipsum dolor sit amet, consectetur adipisicing elitLorem ipsum dolor sit amet, consectetur adipisicing <a href=\"#\">elitLorem ipsum dolor </a>sit amet, consectetur adipisicing elitLorem ipsum dolor sit amet, consectetur adipisicing elit.
<ol>
 	<li>consectetur adipisicing elitLorem ipsum dolor sit amet, consecteturt.</li>
 	<li>ipsum dolor sit amet, consecteturt.</li>
 	<li>Lorem ipsum dolor sit amet</li>
</ol>
<h3>B - Consectetur adipisicing elitLorem ipsum</h3>
Lorem ipsum dolor sit amet, consectetur adipisicing elitLorem ipsum rem ipsum dolor sit amet, consectetur adipisicing elitLorem ipsum dolor sit amet, consectetur adipisicing elit.

&nbsp;
<h3>C - Consectetur adipisicing elitLorem ipsum</h3>
Lorem ipsum dolor sit amet, consectetur adipisicing elitLorem ipsum rem ipsum dolor sit amet, consectetur adipisicing elitLorem ipsum dolor sit amet, consectetur adipisicing elit.
<h3>Conclusion</h3>
Lorem ipsum dolor sit amet, consectetur adipisicing elitLorem ipsum rem ipsum dolor sit amet, consectetur adipisicing elitLorem ipsum dolor sit amet, consectetur adipisicing elit.",
    "publishedAt" => "1472855590",
    "author" => array(
        "id" => 22,
        "fullname" => "Daniela PETIZ",
        "url" => "#",
        "image_uri" => "http://webmatiq.com/lightpost-wordpress-lightbox/images/avatar1.jpg" // if dosen t exist leave empty
    ),
    "categories" => array(
        "0" => array(
            "name" => "Movies",
            "url" => "#"
        ),
        "1" => array(
            "name" => "Action",
            "url" => "#"
        )
    ),
    "overlayImage" => "",
    "media" => array(
        "type" => "image", //youtube, vimeo, embed, image, daillymotion
        "url" => "http://webmatiq.com/lightpost-wordpress-lightbox/images/girls.jpg", // absolute url
        // "url"=> "https://www.youtube.com/embed/a0Tip7zARgQ", // youtube example
        //"url"=> "https://player.vimeo.com/video/154868101?color=F5B535&title=0&byline=0&portrait=0", // vimeo example
        //"embed" => "<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/lDXb3skDU6U\" frameborder=\"0\" allowfullscreen></iframe>" // if type embed
    ),
    "current_user" => array(// if user is not logged just leave empy like -> {}
        "user_id" => 12,
        "fullname" => "Millie Mcdonald",
        "profile_url" => "#", // if there s a profile
        "can_comment" => "true"
    ),
    "next_post" => array(
        "id" => 2,
        "title" => "Amet asperiores, consectetur consequatur corporis"
    )
);


$id2 = array(
    "title" => "Amet asperiores, consectetur consequatur corporis",
    "content" => "<p>Lorem ipsum dolor sit amet, <strong>consectetur adipisicing elit</strong>. Amet asperiores, consectetur consequatur corporis <a href=\"#\">cupiditate</a> eius est eum inventore ipsum labore magni nemo pariatur quaetempore voluptatem. Animi eum reiciendis sunt</p><h2></h2><p>deserunt dignissimos eum facere fuga, ure magni maiores necessitatibus, nobis provident quasi rerum sit temporibus tenetur, velit voluptatum!</p>",
    "publishedAt" => "1472855690",
    "author" => array(
        "id" => 22,
        "fullname" => "Christie Moss",
        "url" => "#",
        "image_uri" => "http://webmatiq.com/lightpost-wordpress-lightbox/images/avatar2.jpg" // if dosen t exist leave empty
    ),
    "categories" => array(
        "0" => array(
            "name" => "animation",
            "url" => "#"
        ),
        "1" => array(
            "name" => "3D",
            "url" => "#"
        )
    ),
    "overlayImage" => "",
    "media" => array(
        "type" => "image", //youtube, vimeo, embed, image, daillymotion
        "url" => "http://webmatiq.com/lightpost-wordpress-lightbox/images/valentine.jpg", // absolute url
        //"url"=> "https://www.youtube.com/embed/nslEhMiN-5M", // youtube example
        //"url"=> "https://player.vimeo.com/video/154868101?color=F5B535&title=0&byline=0&portrait=0", // vimeo example
        "embed" => '<video class="fillWidth" autoplay="autoplay" loop="loop" width="100%" height="auto"><source src="https://s3-us-west-2.amazonaws.com/coverr/mp4/Agua-natural.mp4" type="video/mp4">Your browser does not support the video tag. I suggest you upgrade your browser.</video>' // if type embed
    ),
    "current_user" => array(// if user is not logged just leave empy like -> {}
        "user_id" => 12,
        "fullname" => "Millie Mcdonald",
        "profile_url" => "#", // if there s a profile
        "can_comment" => "true"
    ),
    "next_post" => array(
        "id" => 3,
        "title" => "Amet asperiores, consectetur consequatur corporis"
    ),
    "previous_post" => array(
        "id" => 1,
        "title" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. 21st-century, quam, velit!"
    )
);


$id3 = array(
    "title" => "Amet asperiores, consectetur consequatur corporis",
    "content" => "<p style=\"text-align: center;\">Amet asperiores, consectetur consequatur corporis cupiditate eius est eum inventore ipsum labore magni nemo pariatur quaetempore voluptatem. Animi eum reiciendis sunt.</p>
<p style=\"text-align: center;\">deserunt dignissimos eum facere fuga, iure magni maiores necessitatibus, nobis provident quasi rerum sit temporibus tenetur, velit voluptatum!</p>",
    "publishedAt" => "147286590",
    "author" => array(
        "id" => 22,
        "fullname" => "Evelyn Haney",
        "url" => "#",
        "image_uri" => "http://webmatiq.com/lightpost-wordpress-lightbox/images/avatar1.jpg" // if dosen t exist leave empty
    ),
    "categories" => array(
        "0" => array(
            "name" => "Kitchen",
            "url" => "#"
        ),
        "1" => array(
            "name" => "Recipes",
            "url" => "#"
        ),
        "2" => array(
            "name" => "Dish",
            "url" => "#"
        )
    ),
    "overlayImage" => "",
    "media" => array(
        "type" => "image", //youtube, vimeo, embed, image, daillymotion
        "url" => "http://webmatiq.com/lightpost-wordpress-lightbox/images/plat.jpg",
    ),
    "current_user" => array(// if user is not logged just leave empy like -> {}
        "user_id" => 12,
        "fullname" => "Millie Mcdonald",
        "profile_url" => "#", // if there s a profile
        "can_comment" => "true"
    ),
    "previous_post" => array(
        "id" => 2,
        "title" => "Amet asperiores, consectetur consequatur corporis"
    ),
    "next_post" => array(
        "id" => 4,
        "title" => "Amet asperiores, consectetur consequatur corporis"
    )
);

$id4 = array(
    "title" => "Amet asperiores, consectetur consequatur corporis",
    "content" => "",
    "publishedAt" => "147286590",
    "author" => array(
        "id" => 22,
        "fullname" => "Clayton Herring",
        "url" => "#",
        "image_uri" => "http://webmatiq.com/lightpost-wordpress-lightbox/images/avatar2.jpg" // if dosen t exist leave empty
    ),
    "categories" => array(
        "0" => array(
            "name" => "Design",
            "url" => "#"
        ),
        "1" => array(
            "name" => "Decoration",
            "url" => "#"
        )
    ),
    "media" => array(
        "type" => "embed", //youtube, vimeo, embed, image, daillymotion
        "embed" => '<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d5599.452758809149!2d12.329868027101668!3d45.43501679503034!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sfr!2sfr!4v1477523662710" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>' // if type embed
    ),
    "current_user" => array(// if user is not logged just leave empy like -> {}
        "user_id" => 12,
        "fullname" => "Millie Mcdonald",
        "profile_url" => "#", // if there s a profile
        "can_comment" => "true"
    ),
    "previous_post" => array(
        "id" => 3,
        "title" => "Amet asperiores, consectetur consequatur corporis"
    )
);

if (isset($_POST) && isset($_POST['post_id'])) {
    if ($_POST['post_id'] == 1) echo json_encode($id1);
    elseif ($_POST['post_id'] == 2) echo json_encode($id2);
    elseif ($_POST['post_id'] == 3) echo json_encode($id3);
    elseif ($_POST['post_id'] == 4) echo json_encode($id4);

    else echo json_encode($id1);
} else echo json_encode($id1);


?>