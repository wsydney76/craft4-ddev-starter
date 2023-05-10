export default {
    _mediaCookiesAllowed: null,

    init() {
        this._mediaCookiesAllowed = Cookies.get('mediaCookiesAllowed');
    },

    get mediaCookiesAllowed() {

        // If the user has not made a choice yet, we assume that they have not given consent
        if(this._mediaCookiesAllowed === undefined) {
            return false;
        }

        // Do not use strict comparison here, because the value is a string (maybe)
        return this._mediaCookiesAllowed === 'allowed';
    },

    allowMediaCookies() {
        this._mediaCookiesAllowed = 'allowed'
        Cookies.set('mediaCookiesAllowed', 'allowed', { expires: 365 });
    },
    rejectMediaCookies() {
        this._mediaCookiesAllowed = 'rejected'
        Cookies.set('mediaCookiesAllowed', 'rejected', { expires: 365 });
    },

    get promptForUserChoice() {
        return this._mediaCookiesAllowed === undefined;
    }
}
