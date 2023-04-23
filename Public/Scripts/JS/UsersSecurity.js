/**
 * The Application that is going to be rendered in the DOM
 */
class Application extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            User: {
                username: "",
                mailAddress: "",
                profilePicture: "",
            },
            Password: {
                current: "",
                new: "",
                confirmNew: "",
            },
            System: {
                status: 0,
                message: "",
                url: "",
            },
        };
    }
    /**
     * Retrieving the session's data that is stored as a JSON to be used in the rendering
     * @returns {void}
     */
    getUser() {
        fetch("/Users/CurrentUser", {
            method: "GET",
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    User: {
                        username: data.User.username,
                        mailAddress: data.User.mailAddress,
                        profilePicture: data.User.profilePicture,
                    },
                })
            );
    }
    /**
     * Verifying the state before rendering the link
     * @returns {object}
     */
    verifyState() {
        if (this.state.User.profilePicture != null) {
            return (
                <a href={`/Users/Profile/${this.state.User.username}`}>
                    <img src={this.state.User.profilePicture} />
                </a>
            );
        } else {
            return (
                <a
                    href={`/Users/Profile/${this.state.User.username}`}
                    class="fa fa-user"
                ></a>
            );
        }
    }
    /**
     * Redirecting the user to an intended url
     * @param {number} delay
     * @returns {void}
     */
    redirector(delay) {
        setTimeout(() => {
            window.location.href = this.state.System.url;
        }, delay);
    }
    /**
     * Handling any change that is made in the user interface
     * @param {Event} event
     * @returns {void}
     */
    handleChange(event) {
        const target = event.target;
        const value = target.value;
        const name = target.name;
        if (name == "mailAddress") {
            this.setState((previous) => ({
                ...previous,
                User: {
                    ...previous.User,
                    [name]: value,
                },
            }));
        } else {
            this.setState((previous) => ({
                ...previous,
                Password: {
                    ...previous.Password,
                    [name]: value,
                },
            }));
        }
    }
    /**
     * Handling the form submission
     * @param {Event} event
     * @returns {void}
     */
    handleSubmit(event) {
        const delay = 4075;
        event.preventDefault();
        fetch(`/Users/${this.state.User.username}/Security`, {
            method: "POST",
            body: JSON.stringify({
                mailAddress: this.state.User.mailAddress,
                Password: {
                    old: this.state.Password.current,
                    new: this.state.Password.new,
                    confirmNew: this.state.Password.confirmNew,
                },
            }),
            headers: {
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    System: {
                        status: data.status,
                        message: data.message,
                        url: data.url,
                    },
                })
            )
            .then(() => this.redirector(delay));
    }
    /**
     * Handling the response from the server
     * @returns {string}
     */
    handleResponseColor() {
        if (this.state.System.status == 0) {
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
        if (this.state.System.status == 0) {
            return "71%";
        } else {
            return "180%";
        }
    }
    /**
     * Verifying whether there is a profile picture
     * @returns {object}
     */
    verifyProfilePicture() {
        if (this.state.User.profilePicture != null) {
            return <img src={this.state.User.profilePicture} />;
        } else {
            return <i class="fa fa-user"></i>;
        }
    }
    /**
     * Rendering the application
     * @returns {Application[]}
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
    componentDidMount() {
        this.getUser();
    }
    render() {
        return (
            <header>
                <nav>
                    <div>
                        <a href={`/Users/Home/${this.state.User.username}`}>
                            Parkinston
                        </a>
                    </div>
                    <div>{this.verifyState()}</div>
                    <div>
                        <a href="/Sign-Out" class="fa fa-sign-out"></a>
                    </div>
                </nav>
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
    componentDidMount() {
        this.getUser();
    }
    render() {
        return (
            <main>
                <header>
                    <div id="profilePicture">{this.verifyProfilePicture()}</div>
                    <div id="username">{this.state.User.username}</div>
                </header>
                <form method="POST" onSubmit={this.handleSubmit.bind(this)}>
                    <div id="label">Account Security Form</div>
                    <input
                        type="mail"
                        name="mailAddress"
                        placeholder="Mail Address"
                        value={this.state.User.mailAddress}
                        onChange={this.handleChange.bind(this)}
                    />
                    <input
                        type="password"
                        name="current"
                        placeholder="Old Password"
                        value={this.state.Password.current}
                        onChange={this.handleChange.bind(this)}
                    />
                    <input
                        type="password"
                        name="new"
                        placeholder="New Password"
                        value={this.state.Password.new}
                        onChange={this.handleChange.bind(this)}
                    />
                    <input
                        type="password"
                        name="confirmNew"
                        placeholder="Confirm New Password"
                        value={this.state.Password.confirmNew}
                        onChange={this.handleChange.bind(this)}
                    />
                    <div id="button">
                        <button>Change</button>
                    </div>
                    <div id="response">
                        <h1
                            style={{
                                color: this.handleResponseColor(),
                                fontSize: this.handleResponseFontSize(),
                            }}
                        >
                            {this.state.System.message}
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
