/**
 * The main script that will initialize the application as needed
 */
class Parkinston {
    // Contructor method
    constructor() {
        /**
         * The request URI of the page needed
         * @type {string}
         */
        this.__requestUniformRequestInformation;
        /**
         * The ID of the body
         */
        this.__bodyId;
        /**
         * List of modules needed
         * @type {string[]}
         */
        this._modulesPath = [
            "/Modules/React/18.2.0/react.production.min.js",
            "/Modules/React/18.2.0/react-dom.production.min.js",
            "/Modules/Babel/6.26.0/babel.min.js",
            "/Modules/Font-Awesome/6.2.1/fontawesome.min.js",
        ];
        /**
         * Stylesheets of the application
         * @type {string[]}
         */
        this._stylesheets = [
            "/Public/Stylesheets/parkinston.css",
            "/Public/Stylesheets/desktop.css",
            "/Public/Stylesheets/mobile.css",
            "/Public/Stylesheets/tablet.css",
        ];
        /**
         * Relationship of the object
         * @type {string}
         */
        this.__relationship;
        /**
         * MIME Type of the object
         * @type {string}
         */
        this.__mimeType;
        /**
         * Media queries for the stylesheets
         * @type {string[]}
         */
        this._mediaQueries = [
            "screen and (min-width: 1024px)",
            "screen and (min-width: 640px) and (max-width: 1023px)",
            "screen and (max-width: 639px)",
        ];
        this.init();
    }
    /**
     * Request URI accessor method
     * @returns {string}
     */
    getRequestURI() {
        return this.__requestUniformRequestInformation;
    }
    /**
     * Request URI mutator method
     * @param {string} request_uri
     */
    setRequestURI(request_uri) {
        this.__requestUniformRequestInformation = request_uri;
    }
    /**
     * Body ID accessor method
     * @returns {string}
     */
    getBodyId() {
        return this.__bodyId;
    }
    /**
     * Body ID mutator method
     * @param {string} body_id
     */
    setBodyId(body_id) {
        this.__bodyId = body_id;
    }
    /**
     * Relationship accessor method
     * @returns {string}
     */
    getRelationship() {
        return this.__relationship;
    }
    /**
     * Relationship mutator method
     * @param {string} relationship
     */
    setRelationship(relationship) {
        this.__relationship = relationship;
    }
    /**
     * MIME Type accessor method
     * @returns {string}
     */
    getMimeType() {
        return this.__mimeType;
    }
    /**
     * MIME Type mutator method
     * @param {string} mime_type
     */
    setMimeType(mime_type) {
        this.__mimeType = mime_type;
    }
    /**
     * Initializing the application
     */
    init() {
        this.setRequestURI(window.location.pathname);
        if (this.getRequestURI() == "/") {
            this.setBodyId("Homepage");
        } else {
            this.setBodyId(this.getRequestURI().replaceAll("/", ""));
        }
        document.body.id = this.getBodyId();
        this.mount();
    }
    /**
     * Mounting the modules
     */
    mount() {
        for (let index = 0; index < this._modulesPath.length; index++) {
            const script = document.createElement("script");
            script.src = this._modulesPath[index];
            document.head.appendChild(script);
        }
        this.design();
        this.style();
    }
    /**
     * Styling the application
     */
    style() {
        this.setRelationship("stylesheet");
        this.setMimeType("text/css");
        for (let index = 0; index < this._stylesheets.length; index++) {
            const link = document.createElement("link");
            link.href = this._stylesheets[index];
            if (link.href.includes("desktop")) {
                link.media = this._mediaQueries[0];
            } else if (link.href.includes("mobile")) {
                link.media = this._mediaQueries[2];
            } else if (link.href.includes("tablet")) {
                link.media = this._mediaQueries[1];
            }
            link.rel = this.getRelationship();
            link.type = this.getMimeType();
            document.head.appendChild(link);
        }
    }
    /**
     * Mounting the components
     */
    design() {
        const script = document.createElement("script");
        script.type = "text/babel";
        script.src = `/Public/Scripts/JS/${this.getBodyId()}.js`;
        document.body.appendChild(script);
    }
}
const application = new Parkinston();
