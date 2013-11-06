function Cookie() {
     /*     Set a cookie's value
         
         *  @param  name  string  Cookie's name.
         *  @param  value string  Cookie's value.
         *  @param  days  int     Number of days for expiry.
    */
    this.SetValue = function (name, value, days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            var expires = "; expires=" + date.toGMTString();
        } else var expires = "";
        document.cookie = name + "=" + value + expires + "; path=/";
    }

    /**     Get a cookie's value
         
         *  @param  name  string  Cookie's name.
         *  @return value of the Cookie.
    */
    this.GetValue = function (name) {
        var coname = name + "=";
        var co = document.cookie.split(';');
        for (var i = 0; i < co.length; i++) {
            var c = co[i];
            c = c.replace(/^\s+/, '');
            if (c.indexOf(coname) == 0) return c.substring(coname.length, c.length);
        }
        return null;
    }

    /**     Removes a cookie
         
         *  @param  name  string  Cookie's name.
    */

    this.ClearValue = function (name) {
        manageCookie.SetValue(name, "", -1);
    }

    /** Returns an object with all the cookies. */

    this.GetAll = function () {
        var splits = document.cookie.split(";");
        var cookies = {};
        for (var i = 0; i < splits.length; i++) {
            var split = splits[i].split("=");
            cookies[split[0]] = unescape(split[1]);
        }
        return cookies;
    }

    /** Removes all the cookies */

    this.Clear = function () {
        var cookies = manageCookie.getAll();
        for (var key in cookies) {
            if (obj.hasOwnProperty(key)) {
                manageCookie.removeCookie();
            }
        }
    }
}