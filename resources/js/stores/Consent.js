export default {
    // Variable to hold user's media cookies preference
    _mediaCookiesAllowed: null,

    // Method to initialize the user's media cookies preference from a cookie
    init() {
        // Get the user's media cookies preference from a cookie
        this._mediaCookiesAllowed = Cookies.get('mediaCookiesAllowed');
    },

    // Getter for the user's media cookies preference
    get mediaCookiesAllowed() {
        // If the user has not made a choice yet, we assume that they have not given consent
        if(this._mediaCookiesAllowed === undefined) {
            // Return false as default value
            return false;
        }

        // Return true if the user has allowed media cookies, false otherwise
        return this._mediaCookiesAllowed === 'allowed';
    },

    // Method to allow media cookies
    allowMediaCookies() {
        // Update the variable and set a cookie to remember the user's choice for a year
        this._mediaCookiesAllowed = 'allowed'
        Cookies.set('mediaCookiesAllowed', 'allowed', { expires: 365 });
    },

    // Method to reject media cookies
    rejectMediaCookies() {
        // Update the variable and set a cookie to remember the user's choice for a year
        this._mediaCookiesAllowed = 'rejected'
        Cookies.set('mediaCookiesAllowed', 'rejected', { expires: 365 });
    },

    // Getter to check if the user needs to be prompted for their choice regarding media cookies
    get promptForUserChoice() {
        // The user needs to be prompted if they have not made a choice yet
        return this._mediaCookiesAllowed === undefined;
    }
}
