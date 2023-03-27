/**
 * The Application that is going to be rendered in the DOM
 */
class Application extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            User: {
                username: "",
                profilePicture: "",
            },
            Accounts: {
                PlayerUnknownBattleGrounds: {
                    playerName: "",
                    search: "",
                    Version: {
                        major: 0,
                        minor: 0,
                    },
                },
            },
            System: {
                status: "",
                url: "",
            },
        };
    }
    /**
     * Accessing User data
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
                        profilePicture: data.User.profilePicture,
                    },
                    Accounts: {
                        PlayerUnknownBattleGrounds: {
                            playerName:
                                data.Account.PlayerUnknownBattleGrounds
                                    .playerName,
                        },
                    },
                })
            );
    }
    /**
     * Handling the form submission
     * @param {Event} event
     */
    handleSubmit(event) {
        const delay = 1800;
        event.preventDefault();
        fetch("/PlayerUnknownBattleGrounds/Players", {
            method: "POST",
            body: JSON.stringify({
                pubgSearch:
                    this.state.Accounts.PlayerUnknownBattleGrounds.search,
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
                        url: data.url,
                    },
                })
            )
            .then(() => this.redirector(delay));
    }
    /**
     * Redirecting the user to an intended url
     * @param {number} delay
     */
    redirector(delay) {
        setTimeout(() => {
            window.location.href = this.state.System.url;
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
        this.setState((previous) => ({
            ...previous,
            Accounts: {
                ...previous.Accounts,
                PlayerUnknownBattleGrounds: {
                    ...previous.Accounts.PlayerUnknownBattleGrounds,
                    [name]: value,
                },
            },
        }));
    }
    /**
     * Sending a request to the server to update the data in its cache database before refreshing the page
     */
    updateData() {
        const delay = 1000;
        fetch("/PlayerUnknownBattleGrounds/Refresh", {
            method: "POST",
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    System: {
                        status: data.status,
                        url: data.url,
                    },
                })
            )
            .then(() => this.redirector(delay));
    }
    /**
     * Accessing latest version data
     */
    getVersion() {
        fetch("/PlayerUnknownBattleGrounds/PatchNotes", {
            method: "GET",
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    Accounts: {
                        PlayerUnknownBattleGrounds: {
                            Version: {
                                major: data.major,
                                minor: data.minor,
                            },
                        },
                    },
                })
            );
    }
    /**
     * Renders the components that are being returned
     * @returns {object[]}
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
                    <div>
                        <form
                            method="POST"
                            onSubmit={this.handleSubmit.bind(this)}
                        >
                            <input
                                type="search"
                                name="search"
                                placeholder="Search..."
                                value={
                                    this.state.Accounts
                                        .PlayerUnknownBattleGrounds.search
                                }
                                onChange={this.handleChange.bind(this)}
                                required
                            />
                            <button>
                                <i class="fa fa-search"></i>
                            </button>
                        </form>
                    </div>
                    <div>
                        <button onClick={this.updateData.bind(this)}>
                            Update
                        </button>
                    </div>
                    <div>
                        <PatchNotes />
                    </div>
                    <div>
                        <a
                            href="https://www.pubgesports.com/en/main"
                            target="__blank"
                        >
                            Esports
                        </a>
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
    render() {
        return <main>Main</main>;
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
        return <footer>Footer</footer>;
    }
}
/**
 * Patch Notes component
 */
class PatchNotes extends Header {
    constructor(props) {
        super(props);
        this.state = {
            Accounts: {
                PlayerUnknownBattleGrounds: {
                    Version: {
                        major: 0,
                        minor: 0,
                    },
                },
            },
        };
    }
    componentDidMount() {
        this.getVersion();
    }
    render() {
        return (
            <a
                href={`https://na.battlegrounds.pubg.com/patch-notes/patch-notes-update-${this.state.Accounts.PlayerUnknownBattleGrounds.Version.major}-${this.state.Accounts.PlayerUnknownBattleGrounds.Version.minor}/`}
                target="__blank"
            >
                Patch Notes
            </a>
        );
    }
}
// Rendering the page
ReactDOM.render(<Application />, document.body);
