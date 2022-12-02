/**
 * The Application that is going to be rendered in the DOM
 */
class Application extends React.Component {
    constructor(props) {
        super(props);
        /**
         * States of the properties of the component
         */
        this.state = {
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
        };
    }
    /**
     * Retrieving the session's data that is stored as a JSON to be used in the rendering
     */
    retrieveData() {
        fetch("/Users/CurrentUser",
            {
                method: "GET"
            })
            .then((response) => response.json())
            .then((data) => this.setState({
                username: data.username,
                mailAddress: data.mailAddress,
                domain: data.domain,
                profilePicture: data.profilePicture,
            }));
    }
    /**
     * Methods to be run as soon as the component is mounted
     */
    componentDidMount() {
        this.retrieveData();
    }
    /**
     * Verifying the state before rendering the link
     * @returns {Application} Component
     */
    verifyState() {
        if (this.state.profilePicture != null) {
            return (
                <a href={`/Users/Profile/${this.state.username}`}>
                    <img src={this.state.profilePicture} />
                </a>
            );
        } else {
            return <a href={`/Users/Profile/${this.state.username}`} class="fa fa-user"></a>
        }
    }
    /**
     * Verifying whether there is a profile picture
     * @returns {Application} Component
     */
    verifyProfilePicture() {
        if (this.state.profilePicture != null) {
            return <img src={this.state.profilePicture} />;
        } else {
            return <i class="fa fa-user"></i>
        }
    }
    /**
     * Renders the components that are being returned
     * @returns {Application} Component
     */
    render() {
        return [<Header />, <Main />, <Footer />];
    }
}
/**
 * The component that is the header
 */
class Header extends Application {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <header>
                <NavigationBar />
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
    render() {
        return (
            <main>
                <header>
                    <ProfilePicture />
                    <div id="username">{this.state.username}</div>
                </header>
                <description>
                    <div id="mailAddress">
                        <label>Mail Address:</label>
                        <div>{this.state.mailAddress}</div>
                    </div>
                    <div id="lolUsername">
                        <label>League of Legends's Username:</label>
                        <div>Darkness4869</div>
                    </div>
                </description>
            </main>
        );
    }
}
/**
 * The component that is the footer
 */
class Footer extends Application {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <footer>
                <nav>
                    <a href={`/Users/Edit/Profile/${this.state.username}`}>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        Edit
                    </a>
                    <a href={`/Users/Accounts/${this.state.username}`}>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        Accounts
                    </a>
                    <a href={`/Users/Security/${this.state.username}`}>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        Security
                    </a>
                </nav>
                <div>Parkinston</div>
            </footer>
        );
    }
}
/**
 * The navigation bar component
 */
class NavigationBar extends Header {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <nav>
                <div>
                    <a href={`/Users/Home/${this.state.username}`}>Parkinston</a>
                </div>
                <ProfileLink />
                <div>
                    <a href="/Sign-Out" class="fa fa-sign-out"></a>
                </div>
            </nav>
        );
    }
}
/**
 * The component which will render the profile picture of the user
 */
class ProfileLink extends NavigationBar {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <div>{this.verifyState()}</div>
        );
    }
}
/**
 * Profile Picture component
 */
class ProfilePicture extends Main {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <div id="profilePicture">
                {this.verifyProfilePicture()}
            </div>
        );
    }
}
// Rendering the page
ReactDOM.render(<Application />, document.body);
