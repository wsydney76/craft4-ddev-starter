export default {
    _mediaCookiesAllowed: null,

    init() {
        this._mediaCookiesAllowed = Cookies.get('mediaCookiesAllowed');
    },

    get mediaCookiesAllowed() {
        return (!(this._mediaCookiesAllowed === undefined || this._mediaCookiesAllowed === 0))
    },

    allowMediaCookies() {
        this._mediaCookiesAllowed = 1
        Cookies.set('mediaCookiesAllowed', 1, { expires: 365 });
    },
    rejectMediaCookies() {
        this._mediaCookiesAllowed = 0
        Cookies.set('mediaCookiesAllowed', 0, { expires: 365 });
    },

    get promptForUserChoice() {
        return this._mediaCookiesAllowed === undefined;
    }
}
