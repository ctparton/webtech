/**
* This method utilises D3.JS to create an SVG bar chart of the time the user has 
* spent across projects.
*
* @param {Array} data - An array of objects to create the bar chart from
*
*/
    function createTimeChart(data)
    {
        const width = 1000;
        const height = 500;
        const margin = { top: 50, bottom: 50, left: 25, right: 25 };

        const svg = d3.select('#d3-container')
            .append('svg')
            .attr('width', width - margin.left - margin.right)
            .attr('height', height - margin.top - margin.bottom)
            .attr("viewBox", [0, 0, width, height]);

        const x = d3.scaleBand()
            .domain(d3.range(data.length))
            .range([margin.left, width - margin.right])
            .padding(0.1)

        const y = d3.scaleLinear()
            .domain([0, d3.max(data.map(x => x.time))])
            .range([height - margin.bottom, margin.top])

        svg
            .append("g")
            .attr("fill", '#3bbbe9')
            .selectAll("rect")
            .data(data.sort((a, b) => d3.descending(a.time, b.time)))
            .join("rect")
                .attr("x", (d, i) => x(i))
                .attr("y", d => y(d.time))
                .attr('title', (d) => d.time)
                .attr("class", "rect")
                .attr("height", d => y(0) - y(d.time))
                .attr("width", x.bandwidth());

        function yAxis(g) {
            g.attr("transform", `translate(${margin.left}, 0)`)
                .call(d3.axisLeft(y).ticks(null, data.format))
                .attr("font-size", '20px')
        }

        function xAxis(g) {
            g.attr("transform", `translate(0,${height - margin.bottom})`)
                .call(d3.axisBottom(x).tickFormat(i => data[i].name))
                .attr("font-size", '20px')
        }   

        svg.append("g").call(xAxis);
        svg.append("g").call(yAxis);
        return svg.node();
    };
