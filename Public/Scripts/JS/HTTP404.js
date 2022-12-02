/**
 * The Application that is going to be rendered in the DOM
 */
class Application extends React.Component {
    /**
     * Returns the user backward
     */
    return() {
        history.back();
    }
    /**
     * Renders the components that are being returned
     */
    render() {
        return [<Header />, <Main />, <Footer />];
    }
}
/**
 * The component that is the header
 */
class Header extends Application {
    render() {
        return <header>Parkinston</header>;
    }
}
/**
 * The component that is the main
 */
class Main extends Application {
    render() {
        return (
            <main>
                <div class="Error">404</div>
                <div id="message">Not Found</div>
                <div class="button">
                    <button
                        onClick={this.return()}
                        class="fa fa-angle-left"
                    ></button>
                </div>
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
