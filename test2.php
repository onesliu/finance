<?php
$x = '<?xml version="1.0" encoding="utf-8"?>
<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 width="50px" height="50px" viewBox="244.5 -105.5 50 50" enable-background="new 244.5 -105.5 50 50" xml:space="preserve">
<path style="fill:#ffffff" d="M274.395-70.167c0,1.934-1.568,3.5-3.5,3.5c-1.934,0-3.5-1.566-3.5-3.5c0-1.933,1.566-3.5,3.5-3.5
	C272.826-73.667,274.395-72.1,274.395-70.167z M281.895-73.667c-1.934,0-3.5,1.567-3.5,3.5c0,1.934,1.566,3.5,3.5,3.5
	c1.932,0,3.5-1.566,3.5-3.5C285.395-72.1,283.826-73.667,281.895-73.667z M289.061-85.5c0-1.381-1.119-2.5-2.5-2.5h-20.596
	l-1.076-2.745c-0.289-0.735-0.908-1.292-1.67-1.5l-7.332-2c-1.328-0.362-2.705,0.422-3.07,1.755
	c-0.363,1.332,0.422,2.706,1.754,3.069l6.121,1.67l4.34,11.068c0.375,0.957,1.299,1.587,2.326,1.587h17.537
	c1.381,0,2.5-1.119,2.5-2.5s-1.119-2.5-2.5-2.5h-15.832L267.924-83h18.637C287.941-83,289.061-84.119,289.061-85.5z"/>
</svg>';
$x = str_replace("\r", "", $x);
$x = str_replace("\n", "", $x);
$x = str_replace("\t", " ", $x);
echo $x;
echo "\n";
echo base64_encode($x);
echo "\n";
$b = "PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjUwJSIgeTE9IjAlIiB4Mj0iNTAlIiB5Mj0iMTAwJSI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2ZmZmZmZiIvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iI2U3ZTdlNyIvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxyZWN0IHg9IjAiIHk9IjAiIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JhZCkiIC8+PC9zdmc+IA==";
echo base64_decode($b);
?>