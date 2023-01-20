/**
 * The Application that is going to be rendered in the DOM
 */
class Application extends React.Component {
    constructor(props) {
        super(props);
        /**
         * States of the application
         */
        this.state = {
            /**
             * The url to be redirected after displaying the message
             * @type {string}
             */
            url: "",
            /**
             * one-time password that is sent to the user
             * @type {int}
             */
            oneTimePassword: "",
            /**
             * The status returned from the request
             * @type {int}
             */
            status: 0,
            /**
             * The message that will be displayed to the user
             * @type {string}
             */
            message: "",
            /**
             * Username of the user
             * @type {string}
             */
            username: "",
            /**
             * Mail Address of the user
             * @type {string}
             */
            mailAddress: "",
            /**
             * Domain of the application
             * @type {string}
             */
            domain: "",
            /**
             * User's profile picture
             * @type {string}
             */
            profilePicture: "",
            /**
             * User's League of Legends username
             * @type {string}
             */
            lolUsername: "",
            /**
             * User's League of Legends Region
             * @type {string}
             */
            lolRegion: "",
            /**
             * User's Riot's ID
             * @type {string}
             */
            riotId: "",
        };
    }
    /**
     * Renders the components that are being returned
     * @returns {Application}
     */
    render() {
        return [<Header />, <Main />, <Footer />];
    }
    /**
     * Redirecting the user to an intended url
     * @param {int} delay
     */
    redirector(delay) {
        setTimeout(() => {
            window.location.href = this.state.url;
        }, delay);
    }
    /**
     * Handling any change that is made in the user interface
     * @param {Event} event
     */
    handleChange(event) {
        const target = event.target;
        const value = target.value;
        const name = target.name;
        this.setState({
            [name]: value,
        });
    }
    /**
     * Handling the form submission
     * @param {Event} event
     */
    handleSubmit(event) {
        const delay = 1500;
        event.preventDefault();
        fetch(`/Login/Verification/${this.state.username}`, {
            method: "POST",
            body: JSON.stringify({
                oneTimePassword: this.state.oneTimePassword,
            }),
            headers: {
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    status: data.status,
                    message: data.message,
                    url: data.url,
                })
            )
            .then(() => this.redirector(delay));
    }
    /**
     * Handling the response from the server
     * @returns {string}
     */
    handleResponseColor() {
        if (this.state.status == 0) {
            return "rgb(0%, 100%, 0%)";
        } else {
            return "rgb(100%, 0%, 0%)";
        }
    }
    /**
     * Retrieving the session's data that is stored as a JSON to be used in the rendering
     */
    retrieveData() {
        fetch("/Users/CurrentUser", {
            method: "GET",
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    username: data.User.username,
                    mailAddress: data.User.mailAddress,
                    domain: data.User.domain,
                    profilePicture: data.User.profilePicture,
                    lolUsername: data.Account.LeagueOfLegends.gameName,
                    lolRegion: data.Account.LeagueOfLegends.tagLine,
                    riotId: data.Account.LeagueOfLegends
                        .playerUniversallyUniqueIdentifier,
                })
            );
    }
    /**
     * Handling the response from the server
     * @returns {string}
     */
    handleResponseFontSize() {
        if (this.state.status == 0) {
            return "71%";
        } else {
            return "180%";
        }
    }
}
/**
 * The component that is the header
 */
class Header extends Application {
    render() {
        return (
            <header>
                <a href="/">Parkinston</a>
            </header>
        );
    }
}
/**
 * The component that is the main
 */
class Main extends Application {
    constructor(props) {
        super(props);
    }
    /**
     * Methods to be run as soon as the component is mounted
     */
    componentDidMount() {
        this.retrieveData();
    }
    render() {
        return (
            <main>
                <form method="POST" onSubmit={this.handleSubmit.bind(this)}>
                    <div id="label">Login Form</div>
                    <input
                        type="password"
                        name="oneTimePassword"
                        placeholder="One-Time Password"
                        value={this.state.oneTimePassword}
                        onChange={this.handleChange.bind(this)}
                        required
                    />
                    <div id="button">
                        <button>Login</button>
                    </div>
                    <div id="response">
                        <h1
                            style={{
                                color: this.handleResponseColor(),
                                fontSize: this.handleResponseFontSize(),
                            }}
                        >
                            {this.state.message}
                        </h1>
                    </div>
                </form>
            </main>
        );
    }
}
/**
 * The component that is the footer
 */
class Footer extends Application {
    render() {
        return <footer>Parkinston</footer>;
    }
}
// Rendering the page
ReactDOM.render(<Application />, document.body);
