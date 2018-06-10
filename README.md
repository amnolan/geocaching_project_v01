# geocaching_project
College project for geocache lists using flickr and maps apis. Leverages custom-built pagination and ajax methods.

Essentially a user may input a coordinate and search for geocache locations near them. There is also the ability to filter based on difficulty, distance and cache type.

The application will then add pins to a floating map (the map follows as you scroll). When you click a pin, you will see a pop-up with twelve thumbnails from a flickr integration. By clicking the pin, it will also highlight the list item. Clicking a list item will also trigger the pin to pop up the image pane of the corresponding pin.

The results are paginated as sometimes it is possible to get thousands of results. I handle this through SQL queries and by keeping track of which section of the list has been provided to the user.

The app also uses responsive bootstrap and as a result allows a great mobile experience.

![Start](https://github.com/amnolan/geocaching_project_v01/blob/master/start.png)

Upon searching it will inform you of how many results came back and will create a pagination selector below.

![Search](https://github.com/amnolan/geocaching_project_v01/blob/master/search.png)

The pagination list.

![Pagination](https://github.com/amnolan/geocaching_project_v01/blob/master/paginate.png)

The server side flickr call:

![Flickr](https://github.com/amnolan/geocaching_project_v01/blob/master/flickr.png)

I also ensured to hide plenty of references to _The IT Crowd_ and _Silicon Valley_. Bighead at Hooli, you know what I'm talking about.
