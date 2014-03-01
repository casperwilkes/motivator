/**
 * @author Casper Wilkes <casper@casperwilkes.net> 
 **/

/**
 * Displays guide (instructions) for image upload.
 * @param {string} c The container
 * @param {string} g The guide
 */
function show_guide(c, g) {
    $(c).slideToggle("slow");
    $(g).slideToggle("slow").show();
    $(":button").text("Show Guide" === $(":button").text() ? "Hide Guide" : "Show Guide");
}

/**
 * Displays a loading screen when uploading images.
 * @param {string} c The container
 * @param {string} l The loading screen
 */
function loading(c, l) {
    $(c).slideToggle();
    $(l).slideToggle();
}