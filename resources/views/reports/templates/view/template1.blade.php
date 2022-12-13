<style>
.report {
    color: #5f6873;
    padding: 10px;
}

.report .title p {
    font-size: 1.3rem;
    font-weight: 500;
}

.report .head-item-row {
    padding-top: 1.5rem;
    padding-bottom: 1rem;
}

.report .head-item {
    padding-bottom: 0.5rem;
    font-size: .85em;
    letter-spacing: .1em;
    text-transform: uppercase;
}

.report b, strong, th {
    font-weight: 500;
}

/* table data */
.report table {
    border: 1px solid #a5a5a5;
    border-collapse: collapse;
    margin: 0;
    padding: 0;
    width: 100%;
}

.report .overflow {
    overflow-y: auto;
}

.report table caption {
    font-size: 1.5em;
    margin: .5em 0 .75em;
}

.report table thead tr {
    background-color: #ececec;
}

.report table tr {
    padding: .35em;
    border: 1px solid #a5a5a5;
}

.report table th,
.report table td {
    padding: .425em;
    border-right: 1px solid #a5a5a5;
}

.report table th {
    font-size: .85em;
    letter-spacing: .1em;
    text-transform: uppercase;
    text-align: center;
}

/* mobile */
@media (max-width: 767.98px) {

    .report .title p{
        font-size: 1rem;
    }

    .report table {
        border: 0;
    }

    .report table caption {
        font-size: 1.3em;
    }

    .report table thead {
        border: none;
        clip: rect(0 0 0 0);
        height: 1px;
        margin: -1px;
        overflow: hidden;
        padding: 0;
        position: absolute;
        width: 1px;
    }

    .report table {
        border: 1px solid #ddd;
    }

    .report table tr {
        border: 1px solid #ddd;
        background-color: #f8f8f8;
        border-bottom: 3px solid #ddd;
        display: block;
        margin-bottom: .625em;
    }

    .report table td {
        border-bottom: 1px solid #ddd;
        display: block;
        font-size: .8em;
        text-align: right;
    }

    .report table td::before {
        /*
            * aria-label has no advantage, it won't be read inside a table
            content: attr(aria-label);
            */
        content: attr(data-label);
        float: left;
        font-weight: 500;
        text-transform: uppercase;
    }

    .report table td:last-child {
        border-bottom: 0;
    }

    .report table th,
    .report table td {
        border-right: 0px solid #ddd;
    }
}
</style>
<div class="row m-0 report">
    @yield('content')
</div>
