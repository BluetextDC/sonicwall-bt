<?php
$csv = array_map('str_getcsv', file('rightanswers-alerts.csv'));

$lookup = array();

for ($i = 2; $i < count($csv); $i++)
{
	$slug = createSlug($csv[$i][1]);
	$id = $csv[$i][0];

	if ($slug && strlen($slug) > 0)
	{
		$lookup[$slug] = $id;
	}
}

//Make sure the slug is set
if (isset($_GET['slug']))
{
	$slug = $_GET['slug'];

	//Do a lookup of the slug to get an ID
	if (array_key_exists($slug, $lookup) && $sol_id = $lookup[$slug])
	{
		$url = "/support/product-notification/?sol_id={$sol_id}";
		return redirect($url);
	}
}

//Failed to find redirect - showing an error (this should redirect somewhere else though for production)
echo "Missing Slug: {$slug}";
exit();
// redirect("/support");

//Simple 301 redirect function (should retain 90%-99% of the link ranking with search engines)
function redirect($url)
{
	header("HTTP/1.1 301 Moved Permanently"); 
	header("Location: {$url}"); 
	exit();
}

//Function to create a slug from the title
function createSlug($str, $delimiter = '-'){
    $slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));
    return $slug;
} 

?>
