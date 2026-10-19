using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.RazorPages;

namespace TaskTrackerApp.Pages;

public class TasksModel : PageModel
{
    private static readonly List<TaskItem> _tasks = new()
    {
        new TaskItem { Id = 1, Title = "Finish SWE40006 portfolio report", Priority = "High" },
        new TaskItem { Id = 2, Title = "Deploy app to Azure", Priority = "Medium", Done = true },
        new TaskItem { Id = 3, Title = "Take screenshots for evidence", Priority = "Low" }
    };
    private static int _nextId = 4;
    private static readonly object _lock = new();

    public List<TaskItem> Tasks { get; private set; } = new();
    public int Total { get; private set; }
    public int Completed { get; private set; }
    public int Pending => Total - Completed;
    public int Percent => Total == 0 ? 0 : (int)Math.Round(Completed * 100.0 / Total);

    [BindProperty]
    public string? Title { get; set; }

    [BindProperty]
    public string Priority { get; set; } = "Medium";

    [BindProperty(SupportsGet = true)]
    public string Filter { get; set; } = "all";

    public void OnGet()
    {
        List<TaskItem> all;
        lock (_lock) { all = _tasks.Select(t => t).ToList(); }

        Total = all.Count;
        Completed = all.Count(t => t.Done);

        IEnumerable<TaskItem> view = Filter switch
        {
            "active" => all.Where(t => !t.Done),
            "done" => all.Where(t => t.Done),
            _ => all
        };

        Tasks = view
            .OrderBy(t => t.Done)
            .ThenBy(t => t.Priority == "High" ? 0 : t.Priority == "Medium" ? 1 : 2)
            .ThenByDescending(t => t.Id)
            .ToList();
    }

    public IActionResult OnPostAdd()
    {
        if (!string.IsNullOrWhiteSpace(Title))
        {
            var priority = Priority is "High" or "Medium" or "Low" ? Priority : "Medium";
            lock (_lock)
            {
                _tasks.Add(new TaskItem { Id = _nextId++, Title = Title.Trim(), Priority = priority });
            }
        }
        return RedirectToPage(new { Filter });
    }

    public IActionResult OnPostToggle(int id)
    {
        lock (_lock)
        {
            var t = _tasks.FirstOrDefault(x => x.Id == id);
            if (t != null) t.Done = !t.Done;
        }
        return RedirectToPage(new { Filter });
    }

    public IActionResult OnPostDelete(int id)
    {
        lock (_lock) { _tasks.RemoveAll(x => x.Id == id); }
        return RedirectToPage(new { Filter });
    }

    public IActionResult OnPostClearDone()
    {
        lock (_lock) { _tasks.RemoveAll(x => x.Done); }
        return RedirectToPage(new { Filter });
    }
}

public class TaskItem
{
    public int Id { get; set; }
    public string Title { get; set; } = "";
    public string Priority { get; set; } = "Medium";
    public bool Done { get; set; }
}