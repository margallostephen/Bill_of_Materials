$(function () {
    if (canCopy) return;

    $(document).on("contextmenu copy cut paste", e => e.preventDefault());

    $(document).on("keydown", e => {
        const blocked = [
            123,
            e.ctrlKey && e.shiftKey && [73, 74].includes(e.keyCode),
            e.ctrlKey && [85, 83, 67, 65].includes(e.keyCode),
        ];
        if (blocked.includes(true) || blocked.includes(e.keyCode)) e.preventDefault();
    });

    $(window).on("blur", () => $("body").css("filter", "blur(10px)"))
        .on("focus", () => $("body").css("filter", "none"));

    const $table = $("#bomTable").css("position", "relative");
    const $watermark = $("<div>")
        .text(`CONFIDENTIAL DATA PROPERTY PRIMATECH PHILS., INC.\nLOGGED IN USER: ${userLoggedIn}`)
        .css({
            position: "absolute",
            top: "55%",
            left: "50%",
            transform: "translate(-50%, -50%) rotate(-15deg)",
            color: "rgba(0, 0, 0, 0.05)",
            zIndex: 9999,
            pointerEvents: "none",
            userSelect: "none",
            whiteSpace: "pre-line",
            textAlign: "center",
            width: "100%",
            fontWeight: "700",
        }).appendTo($table);

    const resize = () => {
        const s = Math.min($table.width(), $table.height()) * 0.08;
        $watermark.css("font-size", s + "px");
    };
    resize();
    $(window).on("resize", resize);
    new ResizeObserver(resize).observe($table[0]);

    $("#bomTable, #revisionTable").css({
        "-webkit-user-select": "none",
        "-moz-user-select": "none",
        "-ms-user-select": "none",
        "user-select": "none"
    });
});