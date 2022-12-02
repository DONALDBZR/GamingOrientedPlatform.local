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
            /**
             * Old password of the user
             * @type {string}
             */
            oldPassword: "",
            /**
             * New password of the user
             * @type {string}
             */
            newPassword: "",
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
             * The url to be redirected after displaying the message
             * @type {string}
             */
            url: "",
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
        const delay = 1975;
        event.preventDefault();
        fetch("/Controllers/UsersSecurity.php", {
            method: "POST",
            body: JSON.stringify({
                mailAddress: this.state.mailAddress,
                oldPassword: this.state.oldPassword,
                newPassword: this.state.newPassword,
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
            .then(() => super.redirector(delay));
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
                <Form />
            </main>
        );
    }
}
/**
 * Form component
 */
class Form extends Main {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <form method="POST" onSumbit={this.handleSubmit.bind(this)}>
                <div id="label">Account Security Form</div>
                <input
                    type="mail"
                    name="mailAddress"
                    placeholder="Mail Address"
                    value={this.state.mailAddress}
                    onChange={this.handleChange.bind(this)}
                />
                <input
                    type="password"
                    name="oldPassword"
                    placeholder="Old Password"
                    value={this.state.oldPassword}
                    onChange={this.handleChange.bind(this)}
                />
                <input
                    type="password"
                    name="newPassword"
                    placeholder="New Password"
                    value={this.state.newPassword}
                    onChange={this.handleChange.bind(this)}
                />
                <div id="button">
                    <button>Change</button>
                </div>
                <div id="response">
                    <h1 style={{ color: this.handleResponseColor(), fontSize: this.handleResponseFontSize() }}>{this.state.message}</h1>
                </div>
            </form>
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
